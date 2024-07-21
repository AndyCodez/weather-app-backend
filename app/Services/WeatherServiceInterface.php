<?php

namespace App\Services;

interface WeatherServiceInterface {
    public function getCurrentWeather(float $lat, float $lon, string $unit);
    public function getForecast(float $lat, float $lon, string $unit);
}