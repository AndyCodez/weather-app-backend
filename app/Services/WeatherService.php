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
            'windSpeed' => $data['wind']['speed'] * 3.6, // Convert from m/s to km/h
            'windDegrees' => $data['wind']['deg'],
            'windDirection' => $this->convertToDirection($data['wind']['deg']),
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

            // Skip todays data
            if ($daysCount === 0) {
                $daysCount++;
                continue;
            } 

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
            if ($daysCount === 4) {
                break;
            }
        }

        return array_values($dailyForecast);
    }

    private function convertToDirection(float $degrees): string {
        if ($degrees < 0 || $degrees >= 360) {
            throw new InvalidArgumentException("Degrees must be between 0 and 359.");
        }
    
        $directions = [
            "N", "NNE", "NE", "ENE", "E", "ESE", "SE", "SSE",
            "S", "SSW", "SW", "WSW", "W", "WNW", "NW", "NNW"
        ];
    
        // Calculate the index by dividing degrees by 22.5 and rounding
        $index = round($degrees / 22.5) % 16;
        
        return $directions[$index];
    }

}