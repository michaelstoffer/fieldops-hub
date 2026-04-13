<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function __construct(
        private readonly SubscriptionService $subscriptionService,
    ) {}

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
            // Invoice payments (existing)
            'checkout.session.completed'        => $this->handleCheckoutCompleted($event),
            'payment_intent.payment_failed'     => $this->handlePaymentFailed($event),

            // Subscription lifecycle
            'customer.subscription.created'     => $this->handleSubscriptionCreated($event),
            'customer.subscription.updated'     => $this->handleSubscriptionUpdated($event),
            'customer.subscription.deleted'     => $this->handleSubscriptionDeleted($event),

            default => null,
        };

        return response('OK', 200);
    }

    // ── Invoice payment (existing) ────────────────────────────────────────────

    private function handleCheckoutCompleted(object $event): void
    {
        $session = $event->data->object;

        // Route to subscription activation if this is a subscription checkout
        if (($session->mode ?? null) === 'subscription') {
            $this->handleSubscriptionCheckoutCompleted($session);
            return;
        }

        $invoiceId = $session->metadata->invoice_id ?? null;
        if (! $invoiceId) {
            return;
        }

        $invoice = Invoice::find($invoiceId);
        if (! $invoice) {
            return;
        }

        $amountPaid = $session->amount_total / 100;

        Payment::create([
            'organization_id'          => $invoice->organization_id,
            'invoice_id'               => $invoice->id,
            'amount'                   => $amountPaid,
            'method'                   => Payment::METHOD_STRIPE,
            'stripe_payment_intent_id' => $session->payment_intent,
            'status'                   => 'completed',
            'paid_at'                  => now(),
            'reference'                => $session->id,
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
        // Future: log or notify on failed invoice payment
    }

    // ── Subscription events ───────────────────────────────────────────────────

    /**
     * Subscription checkout session completed — activate the subscription.
     */
    private function handleSubscriptionCheckoutCompleted(object $session): void
    {
        $meta           = $session->metadata ?? null;
        $organizationId = $meta->organization_id ?? null;
        $plan           = $meta->plan ?? null;
        $interval       = $meta->interval ?? 'monthly';

        if (! $organizationId || ! $plan) {
            return;
        }

        // Retrieve the full subscription object from Stripe
        $stripeSubId = $session->subscription ?? null;
        if (! $stripeSubId) {
            return;
        }

        $stripeSub = (new \Stripe\StripeClient(config('services.stripe.secret')))
            ->subscriptions->retrieve($stripeSubId);

        $priceId = $stripeSub->items->data[0]->price->id ?? null;

        $this->subscriptionService->activateFromStripe(
            organizationId: $organizationId,
            stripeSubscriptionId: $stripeSubId,
            plan: $plan,
            interval: $interval,
            stripePriceId: $priceId,
            periodStart: $stripeSub->current_period_start,
            periodEnd: $stripeSub->current_period_end,
        );
    }

    /**
     * Stripe subscription updated — renewal, plan change, status change.
     */
    private function handleSubscriptionCreated(object $event): void
    {
        // Handled via checkout.session.completed; no action needed here
    }

    private function handleSubscriptionUpdated(object $event): void
    {
        $this->subscriptionService->updateFromStripe($event->data->object);
    }

    private function handleSubscriptionDeleted(object $event): void
    {
        $this->subscriptionService->cancelFromStripe($event->data->object);
    }
}
