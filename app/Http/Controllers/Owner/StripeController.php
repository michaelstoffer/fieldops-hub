<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Stripe\Checkout\Session as CheckoutSession;
use Stripe\Stripe;

class StripeController extends Controller
{
    public function createCheckoutSession(Request $request, Invoice $invoice): RedirectResponse
    {
        abort_unless($invoice->organization_id === $request->user()->organization_id, 403);
        abort_unless(in_array($invoice->status, [Invoice::STATUS_SENT, Invoice::STATUS_PARTIAL, Invoice::STATUS_OVERDUE]), 422);
        abort_unless((float) $invoice->balance_due > 0, 422);

        Stripe::setApiKey(config('services.stripe.secret'));

        $invoice->load('lineItems', 'customer');

        $lineItems = $invoice->lineItems->map(fn ($li) => [
            'price_data' => [
                'currency'     => 'usd',
                'product_data' => ['name' => $li->name],
                'unit_amount'  => (int) round((float) $li->unit_price * 100),
            ],
            'quantity' => (int) round((float) $li->quantity),
        ])->values()->all();

        // If totals don't map cleanly to line items (tax/discount), use a single line item for the balance
        $session = CheckoutSession::create([
            'mode'                => 'payment',
            'customer_email'      => $invoice->customer?->email ?? null,
            'line_items'          => $lineItems ?: [[
                'price_data' => [
                    'currency'     => 'usd',
                    'product_data' => ['name' => "Invoice {$invoice->invoice_number}"],
                    'unit_amount'  => (int) round((float) $invoice->balance_due * 100),
                ],
                'quantity' => 1,
            ]],
            'metadata' => [
                'invoice_id'      => $invoice->id,
                'organization_id' => $invoice->organization_id,
            ],
            'success_url' => route('owner.invoices.show', $invoice) . '?payment=success',
            'cancel_url'  => route('owner.invoices.show', $invoice) . '?payment=cancelled',
        ]);

        return redirect($session->url);
    }
}
