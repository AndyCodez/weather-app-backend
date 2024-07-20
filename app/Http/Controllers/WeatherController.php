<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WeatherController extends Controller
{
    public function getWeather(Request $request) {
        return response()->json([
            'location' => 'Nairobi',
            'current_weather' => 'current_weather',
            'forecast' => 'forecast',
        ]);
    }
}
