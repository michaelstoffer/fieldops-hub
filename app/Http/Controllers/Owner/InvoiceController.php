<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\Job;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Response;
use Inertia\ResponseFactory;

class InvoiceController extends Controller
{
    // ── Index ──────────────────────────────────────────────────────────────────

    public function index(Request $request): Response|ResponseFactory
    {
        $orgId = $request->user()->organization_id;

        $stats = Invoice::where('organization_id', $orgId)
            ->selectRaw("
                COUNT(*) as total_count,
                COALESCE(SUM(CASE WHEN status NOT IN ('void','draft') THEN total ELSE 0 END), 0) as total_invoiced,
                COALESCE(SUM(CASE WHEN status IN ('sent','partial','overdue') THEN balance_due ELSE 0 END), 0) as outstanding_balance,
                COALESCE(SUM(CASE WHEN status = 'overdue' THEN balance_due ELSE 0 END), 0) as overdue_balance,
                COALESCE(SUM(CASE WHEN status = 'paid' AND paid_at >= ? THEN total ELSE 0 END), 0) as paid_this_month,
                COUNT(CASE WHEN status IN ('sent','partial','overdue') THEN 1 END) as open_count,
                COUNT(CASE WHEN status = 'overdue' THEN 1 END) as overdue_count
            ", [now()->startOfMonth()])
            ->first();

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
            'stats'    => $stats,
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

    // ── Create ────────────────────────────────────────────────────────────────

    public function create(Request $request): Response|ResponseFactory
    {
        $orgId = $request->user()->organization_id;

        return inertia('Owner/Invoices/Create', [
            'customers'    => Customer::where('organization_id', $orgId)
                ->orderBy('last_name')
                ->get(['id', 'first_name', 'last_name']),
            'catalogItems' => Item::where('organization_id', $orgId)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'unit_price', 'unit', 'is_taxable']),
        ]);
    }

    // ── Store ──────────────────────────────────────────────────────────────────

    public function store(Request $request): RedirectResponse
    {
        $orgId = $request->user()->organization_id;

        $data = $request->validate([
            'customer_id'     => ['required', 'integer', 'exists:customers,id'],
            'issued_at'       => ['required', 'date'],
            'due_at'          => ['required', 'date', 'after_or_equal:issued_at'],
            'tax_rate'        => ['required', 'numeric', 'min:0', 'max:1'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'notes'           => ['nullable', 'string', 'max:2000'],
            'line_items'      => ['required', 'array', 'min:1'],
            'line_items.*.name'       => ['required', 'string', 'max:255'],
            'line_items.*.description' => ['nullable', 'string', 'max:1000'],
            'line_items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'line_items.*.quantity'   => ['required', 'numeric', 'min:0.001'],
            'line_items.*.is_taxable' => ['boolean'],
            'line_items.*.item_id'    => ['nullable', 'integer'],
        ]);

        $invoice = Invoice::create([
            'organization_id' => $orgId,
            'customer_id'     => $data['customer_id'],
            'invoice_number'  => $this->nextInvoiceNumber($orgId),
            'status'          => Invoice::STATUS_DRAFT,
            'tax_rate'        => $data['tax_rate'],
            'discount_amount' => $data['discount_amount'] ?? 0,
            'amount_paid'     => 0,
            'issued_at'       => $data['issued_at'],
            'due_at'          => $data['due_at'],
            'notes'           => $data['notes'] ?? null,
        ]);

        foreach ($data['line_items'] as $idx => $li) {
            $invoice->lineItems()->create([
                'item_id'     => $li['item_id'] ?? null,
                'name'        => $li['name'],
                'description' => $li['description'] ?? null,
                'unit_price'  => $li['unit_price'],
                'quantity'    => $li['quantity'],
                'is_taxable'  => $li['is_taxable'] ?? false,
                'sort_order'  => $idx,
            ]);
        }

        $invoice->recalculate();

        return redirect()->route('owner.invoices.show', $invoice)
            ->with('success', 'Invoice created.');
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

    // ── Record Payment ────────────────────────────────────────────────────────

    public function recordPayment(Request $request, Invoice $invoice): RedirectResponse
    {
        abort_unless($invoice->organization_id === $request->user()->organization_id, 403);
        abort_unless(! in_array($invoice->status, [Invoice::STATUS_VOID, Invoice::STATUS_PAID]), 422);

        $data = $request->validate([
            'amount'    => ['required', 'numeric', 'min:0.01', 'max:' . (float) $invoice->balance_due],
            'method'    => ['required', Rule::in([
                Payment::METHOD_CASH,
                Payment::METHOD_CHECK,
                Payment::METHOD_CARD,
                Payment::METHOD_BANK_TRANSFER,
            ])],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes'     => ['nullable', 'string', 'max:1000'],
            'paid_at'   => ['required', 'date'],
        ]);

        Payment::create([
            'organization_id' => $invoice->organization_id,
            'invoice_id'      => $invoice->id,
            'recorded_by'     => $request->user()->id,
            'amount'          => $data['amount'],
            'method'          => $data['method'],
            'reference'       => $data['reference'] ?? null,
            'notes'           => $data['notes'] ?? null,
            'status'          => 'completed',
            'paid_at'         => $data['paid_at'],
        ]);

        $newAmountPaid = round((float) $invoice->amount_paid + (float) $data['amount'], 2);
        $balanceDue    = max(0, round((float) $invoice->total - $newAmountPaid, 2));

        $invoice->update([
            'amount_paid' => $newAmountPaid,
            'balance_due' => $balanceDue,
            'status'      => $balanceDue <= 0 ? Invoice::STATUS_PAID : Invoice::STATUS_PARTIAL,
            'paid_at'     => $balanceDue <= 0 ? now() : $invoice->paid_at,
        ]);

        return redirect()->route('owner.invoices.show', $invoice)
            ->with('success', 'Payment recorded.');
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
