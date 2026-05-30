<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StoreCustomerRequest;
use App\Http\Requests\Owner\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\ResponseFactory;

class CustomerController extends Controller
{
    public function index(Request $request): Response|ResponseFactory
    {
        $orgId = $request->user()->organization_id;

        $customers = Customer::where('organization_id', $orgId)
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(25)
            ->withQueryString();

        return inertia('Owner/Customers/Index', [
            'customers' => $customers,
            'filters'   => $request->only('search'),
        ]);
    }

    public function show(Request $request, Customer $customer): Response|ResponseFactory
    {
        abort_unless($customer->organization_id === $request->user()->organization_id, 403);

        $customer->load('properties');

        $jobs = $customer->jobs()
            ->with('jobType:id,name,color')
            ->orderByDesc('scheduled_at')
            ->get(['id', 'title', 'status', 'scheduled_at', 'job_type_id']);

        return inertia('Owner/Customers/Show', [
            'customer' => $customer,
            'jobs'     => $jobs,
        ]);
    }

    public function create(): Response|ResponseFactory
    {
        return inertia('Owner/Customers/Create');
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $customer = Customer::create([
            ...$request->validated(),
            'organization_id' => $request->user()->organization_id,
        ]);

        return redirect()->route('owner.customers.show', $customer)
            ->with('success', 'Customer created successfully.');
    }

    public function edit(Request $request, Customer $customer): Response|ResponseFactory
    {
        abort_unless($customer->organization_id === $request->user()->organization_id, 403);

        return inertia('Owner/Customers/Edit', [
            'customer' => $customer,
        ]);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        abort_unless($customer->organization_id === $request->user()->organization_id, 403);

        $customer->update($request->validated());

        return redirect()->route('owner.customers.show', $customer)
            ->with('success', 'Customer updated successfully.');
    }

    public function destroy(Request $request, Customer $customer): RedirectResponse
    {
        abort_unless($customer->organization_id === $request->user()->organization_id, 403);

        $customer->delete();

        return redirect()->route('owner.customers.index')
            ->with('success', 'Customer archived successfully.');
    }

    public function quickCreate(StoreCustomerRequest $request): JsonResponse
    {
        $customer = Customer::create([
            ...$request->validated(),
            'organization_id' => $request->user()->organization_id,
        ]);

        return response()->json([
            'id'         => $customer->id,
            'first_name' => $customer->first_name,
            'last_name'  => $customer->last_name,
            'properties' => [],
        ], 201);
    }

    public function importForm(): Response|ResponseFactory
    {
        return inertia('Owner/Customers/Import');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $path = $request->file('file')->getRealPath();
        $handle = fopen($path, 'r');

        $header = array_map('trim', fgetcsv($handle));
        $allowed = ['first_name', 'last_name', 'email', 'phone', 'mobile', 'notes'];
        $header  = array_map('strtolower', $header);

        $orgId   = $request->user()->organization_id;
        $created = 0;
        $skipped = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($header, array_pad($row, count($header), ''));
            if (empty($data['first_name']) || empty($data['last_name'])) {
                $skipped++;
                continue;
            }
            if (! empty($data['email']) && ! filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $skipped++;
                continue;
            }
            $filtered = array_filter(
                array_intersect_key($data, array_flip($allowed)),
                fn ($v) => $v !== '',
            );
            Customer::create(array_merge($filtered, ['organization_id' => $orgId]));
            $created++;
        }

        fclose($handle);

        return redirect()->route('owner.customers.index')
            ->with('success', "Import complete: {$created} customers added, {$skipped} skipped.");
    }
}
