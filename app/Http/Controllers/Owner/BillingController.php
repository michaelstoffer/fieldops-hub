<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Response;
use Inertia\ResponseFactory;

class BillingController extends Controller
{
    public function index(Request $request): Response|ResponseFactory
    {
        $orgId = $request->user()->organization_id;

        // ── Summary stats ──────────────────────────────────────────────────────

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

        // ── Recent invoices (last 50, filterable) ─────────────────────────────

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

        return inertia('Owner/Billing/Dashboard', [
            'stats'    => $stats,
            'invoices' => $invoices,
            'filters'  => $request->only(['search', 'status']),
            'statuses' => Invoice::statuses(),
        ]);
    }
}
