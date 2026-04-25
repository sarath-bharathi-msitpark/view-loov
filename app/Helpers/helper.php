<?php

use Illuminate\Support\Facades\Http;

if (!function_exists('getAddress')) {
    function getAddress($latitude, $longitude)
    {
        $apiKey = env('MAP_API_KEY');
        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$latitude},{$longitude}&key={$apiKey}";

        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->json();

            if (isset($data['status']) && $data['status'] === 'OK') {
                return $data['results'][0]['formatted_address'] ?? 'Address not found';
            }
        }

        return 'Address not found';
    }
}
