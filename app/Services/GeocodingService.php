<?php

namespace App\Services;

use GuzzleHttp\Client;

class GeocodingService implements GeocodingServiceInterface {

    protected $apiKey;
    protected $client;

    public function __construct() {
        $this->client = new Client();
        $this->apiKey = env('OPENWEATHERMAP_API_KEY');
    }

    public function getCoordinates(string $city): array {
        $response = $this->client->get('https://api.openweathermap.org/data/2.5/weather', [
            'query' => [
                'q' => $city,
                'appid' => $this->apiKey
            ]
        ]);

        $data = json_decode($response->getBody(), true);

        if (isset($data['coord'])) {
            $coords = $data['coord'];
            $cityName = $data['name'];

            return [
                'lat' => $coords['lat'],
                'lon' => $coords['lon'],
                'city' => $cityName
            ];
        }

        throw new \Exception("Could not find city: $city");
    }
}
