<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Services\GeocodingService;
use App\Services\WeatherService;

class WeatherController extends Controller
{
    public function getWeather(Request $request, GeocodingService $geocodingService, WeatherService $weatherService): JsonResponse {

        $city = $request->query('city');
        $units = $request->query('units'); 

        $allowedUnits = ['metric', 'standard', 'imperial'];

        if (!in_array($units, $allowedUnits)) {
            // Default to 'metric'
            $units = 'metric'; 
        }
        
        if (!$city) {
            return response()->json(["error" => "City is required"], 400);
        }

        try {
            $coords = $geocodingService->getCoordinates($city);
            $currentWeatherData = $weatherService->getCurrentWeather($coords['lat'], $coords['lon'], $units);
            $forecastData = $weatherService->getForecast($coords['lat'], $coords['lon'], $units);

            return response()->json([
                'location' => $coords,
                'current_weather' => $currentWeatherData,
                'forecast' => $forecastData,
            ]);
        } catch(\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }
}
