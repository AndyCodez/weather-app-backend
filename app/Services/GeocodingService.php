<?php

namespace App\Services;

use GuzzleHttp\Client;

class GeocodingService implements GeocodingServiceInterface {

    protected $apiKey;
    protected $client;

    public function __construct() {
        $this->client = new Client();
        $this->apiKey = env('OPENCAGE_API_KEY');
    }

    public function getCoordinates(string $city): array {
        $response = $this->client->get('https://api.opencagedata.com/geocode/v1/json', [
            'query' => [
                'q' => $city,
                'key' => $this->apiKey
            ]
        ]);

        $data = json_decode($response->getBody(), true);

        if (isset($data['results'][0]['geometry'])) {
            $coords = $data['results'][0]['geometry'];
            $cityName = $data['results'][0]['formatted'];

            return [
                'lat' => $coords['lat'],
                'lon' => $coords['lng'],
                'city' => $cityName
            ];
        }

        throw new \Exception("Could not find city: $city");
    }
}