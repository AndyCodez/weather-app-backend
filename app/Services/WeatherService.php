<?php

namespace App\Services;

use GuzzleHttp\Client;

class WeatherService implements WeatherServiceInterface {

    protected $apiKey;
    protected $client;

    public function __construct() {
        $this->client = new Client();
        $this->apiKey = env('OPENWEATHERMAP_API_KEY');
    }

    public function getCurrentWeather(float $lat, float $lon, string $unit) {
        $response = $this->client->get('https://api.openweathermap.org/data/2.5/weather', [
            'query' => [
                'lat' => $lat, 
                'lon' => $lon, 
                'unit' => $unit,
                'appid' => $this->apiKey
            ]
        ]);

        $data = json_decode($response->getBody(), true);

        return [
            'temp' => $data['main']['temp'],
            'description' => $data['weather'][0]['description'],
            'date' => date('Y-m-d', $data['dt']),
            'windSpeed' => $data['wind']['speed'],
            'humidity' => $data['main']['humidity'],
        ];
    }

    public function getForecast(float $lat, float $lon, string $unit) {
        $response = $this->client->get('https://api.openweathermap.org/data/2.5/forecast', [
            'query' => [
                'lat' => $lat, 
                'lon' => $lon, 
                'unit' => $unit,
                'appid' => $this->apiKey
            ]
        ]);

        $data = json_decode($response->getBody(), true);

        return $data;
    }
}