<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StoreCustomerRequest;
use App\Http\Requests\Owner\UpdateCustomerRequest;
use App\Models\Customer;
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

        return inertia('Owner/Customers/Show', [
            'customer' => $customer,
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
}
