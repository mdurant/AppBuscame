<?php

namespace App\Services\Geocoding;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Integración con Google Maps Geocoding API.
 * Configurar GOOGLE_MAPS_API_KEY en .env.
 * Uso: GeocodingService::geocode($addressLine, $city, $region, $country)
 */
class GeocodingService
{
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.google.maps_api_key', '');
    }

    public function geocode(?string $addressLine = null, ?string $city = null, ?string $region = null, ?string $country = null): ?array
    {
        if (empty($this->apiKey)) {
            Log::warning('GeocodingService: GOOGLE_MAPS_API_KEY no configurada');

            return null;
        }

        $address = implode(', ', array_filter([$addressLine, $city, $region, $country]));
        if (empty(trim($address))) {
            return null;
        }

        $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
            'address' => $address,
            'key' => $this->apiKey,
        ]);

        if (! $response->successful()) {
            return null;
        }

        $data = $response->json();
        if (($data['status'] ?? '') !== 'OK' || empty($data['results'][0])) {
            return null;
        }

        $result = $data['results'][0];
        $location = $result['geometry']['location'] ?? [];
        $placeId = $result['place_id'] ?? null;

        return [
            'latitude' => $location['lat'] ?? null,
            'longitude' => $location['lng'] ?? null,
            'place_id' => $placeId,
            'formatted_address' => $result['formatted_address'] ?? $address,
        ];
    }
}
