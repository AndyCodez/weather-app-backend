<?php

namespace App\Services;

interface GeocodingServiceInterface
{
    public function getCoordinates(string $city): array;
}