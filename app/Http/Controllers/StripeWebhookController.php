<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request): Response
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret    = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (SignatureVerificationException $e) {
            return response('Invalid signature', 400);
        }

        match ($event->type) {
            'checkout.session.completed'      => $this->handleCheckoutCompleted($event),
            'payment_intent.payment_failed'   => $this->handlePaymentFailed($event),
            default                           => null,
        };

        return response('OK', 200);
    }

    private function handleCheckoutCompleted(object $event): void
    {
        $session   = $event->data->object;
        $invoiceId = $session->metadata->invoice_id ?? null;

        if (! $invoiceId) {
            return;
        }

        $invoice = Invoice::find($invoiceId);

        if (! $invoice) {
            return;
        }

        $amountPaid = $session->amount_total / 100; // Stripe amounts are in cents

        Payment::create([
            'organization_id'           => $invoice->organization_id,
            'invoice_id'                => $invoice->id,
            'amount'                    => $amountPaid,
            'method'                    => Payment::METHOD_STRIPE,
            'stripe_payment_intent_id'  => $session->payment_intent,
            'status'                    => 'completed',
            'paid_at'                   => now(),
            'reference'                 => $session->id,
        ]);

        $newAmountPaid = (float) $invoice->amount_paid + $amountPaid;
        $balanceDue    = max(0, round((float) $invoice->total - $newAmountPaid, 2));

        $invoice->update([
            'amount_paid' => $newAmountPaid,
            'balance_due' => $balanceDue,
            'status'      => $balanceDue <= 0 ? Invoice::STATUS_PAID : Invoice::STATUS_PARTIAL,
            'paid_at'     => $balanceDue <= 0 ? now() : $invoice->paid_at,
        ]);
    }

    private function handlePaymentFailed(object $event): void
    {
        // Payment intent failed — no status change needed on the invoice itself,
        // but we could log or notify here in a future milestone.
        // The invoice remains in its current status (sent/partial/overdue).
    }
}
