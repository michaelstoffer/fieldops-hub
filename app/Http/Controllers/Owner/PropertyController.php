<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StorePropertyRequest;
use App\Http\Requests\Owner\UpdatePropertyRequest;
use App\Models\Customer;
use App\Models\Job;
use App\Models\Property;
use App\Services\GeocodingService;
use Illuminate\Http\JsonResponse;
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

    public function store(StorePropertyRequest $request, Customer $customer, GeocodingService $geocoder): RedirectResponse
    {
        abort_unless($customer->organization_id === $request->user()->organization_id, 403);

        $data = [
            ...$request->validated(),
            'organization_id' => $request->user()->organization_id,
            'country'         => $request->validated('country') ?? 'US',
        ];

        $coords = $geocoder->geocode($this->fullAddress($data));
        if ($coords) {
            [$data['latitude'], $data['longitude']] = $coords;
        }

        $customer->properties()->create($data);

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

    public function update(UpdatePropertyRequest $request, Property $property, GeocodingService $geocoder): RedirectResponse
    {
        abort_unless($property->organization_id === $request->user()->organization_id, 403);

        $data = $request->validated();

        // Re-geocode if any address field changed
        $addressFields = ['address_line1', 'address_line2', 'city', 'state', 'postal_code'];
        $addressChanged = collect($addressFields)->contains(
            fn ($field) => array_key_exists($field, $data) && $data[$field] !== $property->$field
        );

        if ($addressChanged) {
            $merged = array_merge($property->toArray(), $data);
            $coords = $geocoder->geocode($this->fullAddress($merged));
            if ($coords) {
                [$data['latitude'], $data['longitude']] = $coords;
            }
        }

        $property->update($data);

        return redirect()->route('owner.customers.show', $property->customer_id)
            ->with('success', 'Property updated successfully.');
    }

    private function fullAddress(array $data): string
    {
        return implode(', ', array_filter([
            $data['address_line1'] ?? null,
            $data['address_line2'] ?? null,
            $data['city'] ?? null,
            $data['state'] ?? null,
            $data['postal_code'] ?? null,
            $data['country'] ?? 'US',
        ]));
    }

    public function quickCreate(StorePropertyRequest $request, Customer $customer, GeocodingService $geocoder): JsonResponse
    {
        abort_unless($customer->organization_id === $request->user()->organization_id, 403);

        $data = [
            ...$request->validated(),
            'organization_id' => $request->user()->organization_id,
            'country'         => $request->validated('country') ?? 'US',
        ];

        $coords = $geocoder->geocode($this->fullAddress($data));
        if ($coords) {
            [$data['latitude'], $data['longitude']] = $coords;
        }

        $property = $customer->properties()->create($data);

        return response()->json([
            'id'            => $property->id,
            'address_line1' => $property->address_line1,
            'city'          => $property->city,
            'state'         => $property->state,
        ], 201);
    }

    public function destroy(Request $request, Property $property): RedirectResponse
    {
        abort_unless($property->organization_id === $request->user()->organization_id, 403);

        $hasActiveJobs = Job::where('property_id', $property->id)
            ->whereNotIn('status', [Job::STATUS_COMPLETED, Job::STATUS_CANCELLED])
            ->exists();

        abort_if($hasActiveJobs, 422, 'Cannot delete a property with active jobs. Complete or cancel them first.');

        $customerId = $property->customer_id;
        $property->delete();

        return redirect()->route('owner.customers.show', $customerId)
            ->with('success', 'Property removed successfully.');
    }
}
