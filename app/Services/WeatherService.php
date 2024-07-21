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

    public function getCurrentWeather(float $lat, float $lon, string $units): array {
        $response = $this->client->get('https://api.openweathermap.org/data/2.5/weather', [
            'query' => [
                'lat' => $lat, 
                'lon' => $lon, 
                'units' => $units,
                'appid' => $this->apiKey
            ]
        ]);

        $data = json_decode($response->getBody(), true);

        return [
            'temp' => $data['main']['temp'],
            'description' => $data['weather'][0]['description'],
            'icon' => $data['weather'][0]['icon'],
            'date' => date('Y-m-d', $data['dt']),
            'windSpeed' => $data['wind']['speed'],
            'humidity' => $data['main']['humidity'],
        ];
    }

    public function getForecast(float $lat, float $lon, string $units): array {
        $response = $this->client->get('https://api.openweathermap.org/data/2.5/forecast', [
            'query' => [
                'lat' => $lat, 
                'lon' => $lon, 
                'units' => $units,
                'appid' => $this->apiKey
            ]
        ]);

        $data = json_decode($response->getBody(), true);

        return $this->getThreeDayForecast($data);
    }

    private function getThreeDayForecast(array $forecastData): array {
        $dailyForecast = [];
        $daysCount = 0;

        foreach ($forecastData['list'] as $forecast) {
            $date = date('Y-m-d', $forecast['dt']);

            if (!isset($dailyForecast[$date])) {
                $dailyForecast[$date] = [
                    'date' => $date,
                    'tempMin' => $forecast['main']['temp_min'],
                    'tempMax' => $forecast['main']['temp_max'],
                    'description' => $forecast['weather'][0]['description'],
                    'icon' => $forecast['weather'][0]['icon'],
                ];
                $daysCount++;
            } else {
                $dailyForecast[$date]['tempMin'] = min($dailyForecast[$date]['tempMin'], $forecast['main']['temp_min']);
                $dailyForecast[$date]['tempMax'] = max($dailyForecast[$date]['tempMax'], $forecast['main']['temp_max']);
            }

            // Stop processing after 3 days
            if ($daysCount === 3) {
                break;
            }
        }

        return array_values($dailyForecast);
    }

}