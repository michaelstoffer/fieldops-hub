<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StorePropertyRequest;
use App\Http\Requests\Owner\UpdatePropertyRequest;
use App\Models\Customer;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\ResponseFactory;

class PropertyController extends Controller
{
    public function create(Request $request, Customer $customer): Response|ResponseFactory
    {
        abort_unless($customer->organization_id === $request->user()->organization_id, 403);

        return inertia('Owner/Properties/Create', [
            'customer' => $customer,
        ]);
    }

    public function store(StorePropertyRequest $request, Customer $customer): RedirectResponse
    {
        abort_unless($customer->organization_id === $request->user()->organization_id, 403);

        $customer->properties()->create([
            ...$request->validated(),
            'organization_id' => $request->user()->organization_id,
            'country'         => $request->validated('country') ?? 'US',
        ]);

        return redirect()->route('owner.customers.show', $customer)
            ->with('success', 'Property added successfully.');
    }

    public function edit(Request $request, Property $property): Response|ResponseFactory
    {
        abort_unless($property->organization_id === $request->user()->organization_id, 403);

        return inertia('Owner/Properties/Edit', [
            'property' => $property,
            'customer' => $property->customer,
        ]);
    }

    public function update(UpdatePropertyRequest $request, Property $property): RedirectResponse
    {
        abort_unless($property->organization_id === $request->user()->organization_id, 403);

        $property->update($request->validated());

        return redirect()->route('owner.customers.show', $property->customer_id)
            ->with('success', 'Property updated successfully.');
    }

    public function destroy(Request $request, Property $property): RedirectResponse
    {
        abort_unless($property->organization_id === $request->user()->organization_id, 403);

        $customerId = $property->customer_id;
        $property->delete();

        return redirect()->route('owner.customers.show', $customerId)
            ->with('success', 'Property removed successfully.');
    }
}
