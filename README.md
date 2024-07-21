# Weather App Backend

This is the backend part of the Weather App built using Laravel. The app provides weather data and geocoding services to the [frontend](https://github.com/AndyCodez/weather-app).

## Features

- Fetch current weather and 3-day forecast from OpenWeatherMap API.
- Geocode city names to get their coordinates.
- Return detailed weather information including temperature, wind status, and humidity.

## Tech Stack

- **Framework**: Laravel
- **Language**: PHP
- **API**: OpenWeatherMap API, Geocoding API

## Setup instructions
1. Clone Repo
2. Install dependencies
```
composer install
```

3. Environment Variables

Add the OpenWeatherMap api key in your .env file in the root directory:

```
OPENWEATHERMAP_API_KEY=your_openweathermap_api_key
```

4. Generate application key
```

php artisan key:generate
```
5. Run database migrations

```

php artisan migrate
```
6. Serve the application
```
php artisan serve
```
