<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Services\GeocodingService;

class WeatherController extends Controller
{
    public function getWeather(Request $request, GeocodingService $geocodingService): JsonResponse {

        $city = $request->query('city');
        
        if (!$city) {
            return response()->json(["error" => "City is required"], 400);
        }

        try {
            $coords = $geocodingService->getCoordinates($city);
            return response()->json([
                'location' => $coords,
                'current_weather' => 'current_weather',
                'forecast' => 'forecast',
            ]);
        } catch(\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }
}
