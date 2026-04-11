<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Estimate;
use App\Models\EstimateLineItem;
use App\Models\EstimatePackage;
use App\Models\Item;
use App\Models\Job;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Response;
use Inertia\ResponseFactory;

class EstimateController extends Controller
{
    // ── Index ─────────────────────────────────────────────────────────────────

    public function index(Request $request): Response|ResponseFactory
    {
        $orgId = $request->user()->organization_id;

        $estimates = Estimate::where('organization_id', $orgId)
            ->with(['customer'])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->search, function ($q, $search) {
                $q->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('estimate_number', 'like', "%{$search}%")
                        ->orWhereHas('customer', fn ($q) =>
                            $q->where('first_name', 'like', "%{$search}%")
                              ->orWhere('last_name', 'like', "%{$search}%")
                        );
                });
            })
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        return inertia('Owner/Estimates/Index', [
            'estimates' => $estimates,
            'filters'   => $request->only(['search', 'status']),
            'statuses'  => Estimate::statuses(),
        ]);
    }

    // ── Show ──────────────────────────────────────────────────────────────────

    public function show(Request $request, Estimate $estimate): Response|ResponseFactory
    {
        abort_unless($estimate->organization_id === $request->user()->organization_id, 403);

        $estimate->load(['customer', 'job', 'packages.lineItems', 'convertedJob']);

        return inertia('Owner/Estimates/Show', [
            'estimate' => $estimate,
            'statuses' => Estimate::statuses(),
        ]);
    }

    // ── Create / Store ────────────────────────────────────────────────────────

    public function create(Request $request): Response|ResponseFactory
    {
        $orgId = $request->user()->organization_id;

        return inertia('Owner/Estimates/Create', [
            'customers' => Customer::where('organization_id', $orgId)
                ->orderBy('last_name')
                ->get(['id', 'first_name', 'last_name']),
            'jobs' => Job::where('organization_id', $orgId)
                ->whereNotIn('status', [Job::STATUS_CANCELLED, Job::STATUS_COMPLETED])
                ->orderByDesc('scheduled_at')
                ->get(['id', 'title', 'scheduled_at', 'customer_id']),
            'catalogItems' => Item::where('organization_id', $orgId)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'unit_price', 'unit', 'is_taxable']),
            'tiers'    => Estimate::TIERS,
            'statuses' => Estimate::statuses(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $orgId = $request->user()->organization_id;

        $data = $request->validate([
            'customer_id'      => ['required', 'integer', Rule::exists('customers', 'id')->where('organization_id', $orgId)],
            'job_id'           => ['nullable', 'integer', Rule::exists('field_jobs', 'id')->where('organization_id', $orgId)],
            'title'            => ['required', 'string', 'max:255'],
            'intro'            => ['nullable', 'string', 'max:2000'],
            'footer'           => ['nullable', 'string', 'max:2000'],
            'expires_at'       => ['nullable', 'date'],
            'tax_rate'         => ['nullable', 'numeric', 'min:0', 'max:1'],
            'packages'         => ['required', 'array', 'min:1'],
            'packages.*.tier'          => ['required', Rule::in(Estimate::TIERS)],
            'packages.*.label'         => ['required', 'string', 'max:100'],
            'packages.*.description'   => ['nullable', 'string', 'max:1000'],
            'packages.*.is_recommended'=> ['boolean'],
            'packages.*.line_items'    => ['present', 'array'],
            'packages.*.line_items.*.name'       => ['required', 'string', 'max:255'],
            'packages.*.line_items.*.description'=> ['nullable', 'string', 'max:500'],
            'packages.*.line_items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'packages.*.line_items.*.quantity'   => ['required', 'numeric', 'min:0.001'],
            'packages.*.line_items.*.is_taxable' => ['boolean'],
            'packages.*.line_items.*.item_id'    => ['nullable', 'integer', 'exists:items,id'],
        ]);

        $estimate = Estimate::create([
            'organization_id' => $orgId,
            'customer_id'     => $data['customer_id'],
            'job_id'          => $data['job_id'] ?? null,
            'title'           => $data['title'],
            'intro'           => $data['intro'] ?? null,
            'footer'          => $data['footer'] ?? null,
            'expires_at'      => $data['expires_at'] ?? null,
            'tax_rate'        => $data['tax_rate'] ?? 0,
            'status'          => Estimate::STATUS_DRAFT,
            'estimate_number' => $this->nextEstimateNumber($orgId),
        ]);

        $this->syncPackages($estimate, $data['packages']);

        return redirect()->route('owner.estimates.show', $estimate);
    }

    // ── Edit / Update ─────────────────────────────────────────────────────────

    public function edit(Request $request, Estimate $estimate): Response|ResponseFactory
    {
        abort_unless($estimate->organization_id === $request->user()->organization_id, 403);

        $orgId = $request->user()->organization_id;
        $estimate->load(['packages.lineItems']);

        return inertia('Owner/Estimates/Edit', [
            'estimate'  => $estimate,
            'customers' => Customer::where('organization_id', $orgId)
                ->orderBy('last_name')
                ->get(['id', 'first_name', 'last_name']),
            'jobs' => Job::where('organization_id', $orgId)
                ->whereNotIn('status', [Job::STATUS_CANCELLED, Job::STATUS_COMPLETED])
                ->orderByDesc('scheduled_at')
                ->get(['id', 'title', 'scheduled_at', 'customer_id']),
            'catalogItems' => Item::where('organization_id', $orgId)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'unit_price', 'unit', 'is_taxable']),
            'tiers'    => Estimate::TIERS,
            'statuses' => Estimate::statuses(),
        ]);
    }

    public function update(Request $request, Estimate $estimate): RedirectResponse
    {
        abort_unless($estimate->organization_id === $request->user()->organization_id, 403);

        $orgId = $request->user()->organization_id;

        $data = $request->validate([
            'customer_id'      => ['required', 'integer', Rule::exists('customers', 'id')->where('organization_id', $orgId)],
            'job_id'           => ['nullable', 'integer', Rule::exists('field_jobs', 'id')->where('organization_id', $orgId)],
            'title'            => ['required', 'string', 'max:255'],
            'intro'            => ['nullable', 'string', 'max:2000'],
            'footer'           => ['nullable', 'string', 'max:2000'],
            'expires_at'       => ['nullable', 'date'],
            'tax_rate'         => ['nullable', 'numeric', 'min:0', 'max:1'],
            'packages'         => ['required', 'array', 'min:1'],
            'packages.*.tier'          => ['required', Rule::in(Estimate::TIERS)],
            'packages.*.label'         => ['required', 'string', 'max:100'],
            'packages.*.description'   => ['nullable', 'string', 'max:1000'],
            'packages.*.is_recommended'=> ['boolean'],
            'packages.*.line_items'    => ['present', 'array'],
            'packages.*.line_items.*.name'       => ['required', 'string', 'max:255'],
            'packages.*.line_items.*.description'=> ['nullable', 'string', 'max:500'],
            'packages.*.line_items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'packages.*.line_items.*.quantity'   => ['required', 'numeric', 'min:0.001'],
            'packages.*.line_items.*.is_taxable' => ['boolean'],
            'packages.*.line_items.*.item_id'    => ['nullable', 'integer', 'exists:items,id'],
        ]);

        $estimate->update([
            'customer_id' => $data['customer_id'],
            'job_id'      => $data['job_id'] ?? null,
            'title'       => $data['title'],
            'intro'       => $data['intro'] ?? null,
            'footer'      => $data['footer'] ?? null,
            'expires_at'  => $data['expires_at'] ?? null,
            'tax_rate'    => $data['tax_rate'] ?? 0,
        ]);

        $this->syncPackages($estimate, $data['packages']);

        return redirect()->route('owner.estimates.show', $estimate);
    }

    // ── Send ──────────────────────────────────────────────────────────────────

    public function send(Request $request, Estimate $estimate): RedirectResponse
    {
        abort_unless($estimate->organization_id === $request->user()->organization_id, 403);

        $estimate->update([
            'status'  => Estimate::STATUS_SENT,
            'sent_at' => now(),
        ]);

        // TODO: dispatch EstimateSent notification (email/SMS) in Milestone 5

        return redirect()->route('owner.estimates.show', $estimate)
            ->with('success', 'Estimate sent.');
    }

    // ── Convert to Job ────────────────────────────────────────────────────────

    public function convertToJob(Request $request, Estimate $estimate): RedirectResponse
    {
        abort_unless($estimate->organization_id === $request->user()->organization_id, 403);
        abort_unless($estimate->status === Estimate::STATUS_ACCEPTED, 422);
        abort_unless($estimate->convertedJob === null, 422);

        $estimate->load('packages.lineItems');

        $package = $estimate->packages
            ->firstWhere('tier', $estimate->accepted_package)
            ?? $estimate->packages->first();

        abort_if($package === null, 422);

        $job = Job::create([
            'organization_id' => $estimate->organization_id,
            'customer_id'     => $estimate->customer_id,
            'estimate_id'     => $estimate->id,
            'title'           => $estimate->title,
            'description'     => $package->description,
            'status'          => Job::STATUS_SCHEDULED,
            'office_notes'    => $estimate->footer,
        ]);

        foreach ($package->lineItems as $idx => $li) {
            $job->lineItems()->create([
                'item_id'    => $li->item_id,
                'name'       => $li->name,
                'description'=> $li->description,
                'unit_price' => $li->unit_price,
                'quantity'   => $li->quantity,
                'sort_order' => $idx,
            ]);
        }

        return redirect()->route('owner.jobs.show', $job)
            ->with('success', 'Job created from estimate.');
    }

    // ── Destroy ───────────────────────────────────────────────────────────────

    public function destroy(Request $request, Estimate $estimate): RedirectResponse
    {
        abort_unless($estimate->organization_id === $request->user()->organization_id, 403);

        $estimate->delete();

        return redirect()->route('owner.estimates.index');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function nextEstimateNumber(int $orgId): string
    {
        $last = Estimate::withTrashed()
            ->where('organization_id', $orgId)
            ->whereNotNull('estimate_number')
            ->orderByDesc('id')
            ->value('estimate_number');

        if ($last && preg_match('/(\d+)$/', $last, $m)) {
            $next = (int) $m[1] + 1;
        } else {
            $next = 1;
        }

        return 'EST-' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    private function syncPackages(Estimate $estimate, array $packagesData): void
    {
        // Delete existing packages (cascades to line items)
        $estimate->packages()->delete();

        foreach ($packagesData as $pkgData) {
            $package = $estimate->packages()->create([
                'tier'           => $pkgData['tier'],
                'label'          => $pkgData['label'],
                'description'    => $pkgData['description'] ?? null,
                'is_recommended' => $pkgData['is_recommended'] ?? false,
                'subtotal'       => 0,
                'tax_amount'     => 0,
                'total'          => 0,
            ]);

            foreach (($pkgData['line_items'] ?? []) as $idx => $liData) {
                $package->lineItems()->create([
                    'item_id'     => $liData['item_id'] ?? null,
                    'name'        => $liData['name'],
                    'description' => $liData['description'] ?? null,
                    'unit_price'  => $liData['unit_price'],
                    'quantity'    => $liData['quantity'],
                    'is_taxable'  => $liData['is_taxable'] ?? true,
                    'sort_order'  => $idx,
                ]);
            }

            $package->recalculate();
        }
    }
}
