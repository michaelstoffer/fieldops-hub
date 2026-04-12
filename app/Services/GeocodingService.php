<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocodingService
{
    public function __construct(private readonly string $apiKey) {}

    /**
     * Geocode an address string and return [lat, lng] or null on failure.
     *
     * @return array{0: float, 1: float}|null
     */
    public function geocode(string $address): ?array
    {
        if (empty($this->apiKey)) {
            return null;
        }

        $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
            'address' => $address,
            'key'     => $this->apiKey,
        ]);

        if (! $response->successful()) {
            Log::warning('Geocoding request failed', ['status' => $response->status()]);
            return null;
        }

        $data = $response->json();

        if (($data['status'] ?? '') !== 'OK' || empty($data['results'])) {
            Log::warning('Geocoding returned no results', ['status' => $data['status'] ?? 'unknown', 'address' => $address]);
            return null;
        }

        $location = $data['results'][0]['geometry']['location'];

        return [(float) $location['lat'], (float) $location['lng']];
    }
}
