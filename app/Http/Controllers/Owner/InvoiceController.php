<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Job;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\ResponseFactory;

class InvoiceController extends Controller
{
    // ── Index ──────────────────────────────────────────────────────────────────

    public function index(Request $request): Response|ResponseFactory
    {
        $orgId = $request->user()->organization_id;

        $invoices = Invoice::where('organization_id', $orgId)
            ->with(['customer', 'job'])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->search, function ($q, $search) {
                $q->where(function ($q) use ($search) {
                    $q->where('invoice_number', 'like', "%{$search}%")
                        ->orWhereHas('customer', fn ($q) =>
                            $q->where('first_name', 'like', "%{$search}%")
                              ->orWhere('last_name', 'like', "%{$search}%")
                        );
                });
            })
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        return inertia('Owner/Invoices/Index', [
            'invoices' => $invoices,
            'filters'  => $request->only(['search', 'status']),
            'statuses' => Invoice::statuses(),
        ]);
    }

    // ── Show ───────────────────────────────────────────────────────────────────

    public function show(Request $request, Invoice $invoice): Response|ResponseFactory
    {
        abort_unless($invoice->organization_id === $request->user()->organization_id, 403);

        $invoice->load(['customer', 'job', 'lineItems', 'payments.recordedBy']);

        return inertia('Owner/Invoices/Show', [
            'invoice'  => $invoice,
            'statuses' => Invoice::statuses(),
        ]);
    }

    // ── Generate from Job ──────────────────────────────────────────────────────

    public function generateFromJob(Request $request, Job $job): RedirectResponse
    {
        abort_unless($job->organization_id === $request->user()->organization_id, 403);
        abort_unless($job->isCompleted(), 422);
        abort_unless($job->invoice === null, 422);

        $job->load('lineItems');

        $invoice = Invoice::create([
            'organization_id' => $job->organization_id,
            'customer_id'     => $job->customer_id,
            'job_id'          => $job->id,
            'invoice_number'  => $this->nextInvoiceNumber($job->organization_id),
            'status'          => Invoice::STATUS_DRAFT,
            'tax_rate'        => 0,
            'discount_amount' => 0,
            'amount_paid'     => 0,
            'issued_at'       => today(),
            'due_at'          => today()->addDays(30),
        ]);

        foreach ($job->lineItems as $idx => $li) {
            $invoice->lineItems()->create([
                'item_id'     => $li->item_id,
                'name'        => $li->name,
                'description' => $li->description,
                'unit_price'  => $li->unit_price,
                'quantity'    => $li->quantity,
                'is_taxable'  => true,
                'sort_order'  => $idx,
            ]);
        }

        $invoice->recalculate();

        return redirect()->route('owner.invoices.show', $invoice)
            ->with('success', 'Invoice generated.');
    }

    // ── Send ───────────────────────────────────────────────────────────────────

    public function send(Request $request, Invoice $invoice): RedirectResponse
    {
        abort_unless($invoice->organization_id === $request->user()->organization_id, 403);
        abort_unless(in_array($invoice->status, [Invoice::STATUS_DRAFT, Invoice::STATUS_OVERDUE]), 422);

        $invoice->update([
            'status'  => Invoice::STATUS_SENT,
            'sent_at' => now(),
        ]);

        // TODO: dispatch InvoiceSent notification in future milestone

        return redirect()->route('owner.invoices.show', $invoice)
            ->with('success', 'Invoice sent.');
    }

    // ── Void ──────────────────────────────────────────────────────────────────

    public function void(Request $request, Invoice $invoice): RedirectResponse
    {
        abort_unless($invoice->organization_id === $request->user()->organization_id, 403);
        abort_unless($invoice->status !== Invoice::STATUS_VOID, 422);
        abort_unless($invoice->status !== Invoice::STATUS_PAID, 422);

        $invoice->update(['status' => Invoice::STATUS_VOID]);

        return redirect()->route('owner.invoices.show', $invoice)
            ->with('success', 'Invoice voided.');
    }

    // ── Destroy ───────────────────────────────────────────────────────────────

    public function destroy(Request $request, Invoice $invoice): RedirectResponse
    {
        abort_unless($invoice->organization_id === $request->user()->organization_id, 403);
        abort_unless($invoice->status === Invoice::STATUS_DRAFT, 422);

        $invoice->delete();

        return redirect()->route('owner.invoices.index')
            ->with('success', 'Invoice deleted.');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function nextInvoiceNumber(int $orgId): string
    {
        $last = Invoice::withTrashed()
            ->where('organization_id', $orgId)
            ->whereNotNull('invoice_number')
            ->orderByDesc('id')
            ->value('invoice_number');

        if ($last && preg_match('/(\d+)$/', $last, $m)) {
            $next = (int) $m[1] + 1;
        } else {
            $next = 1;
        }

        return 'INV-' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
