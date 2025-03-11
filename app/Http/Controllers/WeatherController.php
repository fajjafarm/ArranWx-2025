<?php
namespace App\Http\Controllers;

use App\Services\WeatherService;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class WeatherController extends Controller
{
    protected $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }
    public function index()
    {
        $locations = Location::all();
        $weatherData = [];

        foreach ($locations as $location) {
            // Cache weather data for 1 hour (3600 seconds)
            $weatherCacheKey = "weather_{$location->latitude}_{$location->longitude}";
            $weather = Cache::remember($weatherCacheKey, 3600, function () use ($location) {
                return $this->weatherService->getWeather($location->latitude, $location->longitude);
            });
            $weatherDetails = $weather['properties']['timeseries'][0]['data']['instant']['details'];

            $marine = null;
            if ($location->type === 'Marine') {
                // Cache marine data for 1 hour (3600 seconds)
                $marineCacheKey = "marine_{$location->latitude}_{$location->longitude}";
                $marineResponse = Cache::remember($marineCacheKey, 3600, function () use ($location) {
                    return $this->weatherService->getMarineForecast($location->latitude, $location->longitude);
                });
                if ($marineResponse) {
                    $marine = [
                        'wave_height' => $marineResponse['hourly']['wave_height'][0] ?? null,
                        'wave_direction' => $marineResponse['hourly']['wave_direction'][0] ?? null,
                        'wave_period' => $marineResponse['hourly']['wave_period'][0] ?? null,
                        'wind_wave_height' => $marineResponse['hourly']['wind_wave_height'][0] ?? null,
                        'swell_wave_height' => $marineResponse['hourly']['swell_wave_height'][0] ?? null,
                        'swell_wave_direction' => $marineResponse['hourly']['swell_wave_direction'][0] ?? null,
                        'swell_wave_period' => $marineResponse['hourly']['swell_wave_period'][0] ?? null,
                        'water_temperature' => $marineResponse['hourly']['water_temperature'][0] ?? null,
                    ];
                }
            }

            $weatherData[$location->name] = [
                'weather' => $weatherDetails,
                'marine' => $marine,
                'type' => $location->type,
                'altitude' => $location->altitude ?? 0,
            ];
        }

        return view('dashboard', compact('weatherData', 'locations'));
    }

    public function show($name)
    {
        $location = Location::where('name', $name)->firstOrFail();

        // Cache current weather data for 1 hour
        $weatherCacheKey = "weather_{$location->latitude}_{$location->longitude}";
        $weather = Cache::remember($weatherCacheKey, 3600, function () use ($location) {
            return $this->weatherService->getWeather($location->latitude, $location->longitude);
        });
        $currentWeather = $weather['properties']['timeseries'][0]['data']['instant']['details'];

        // Cache 10-day forecast data for 1 hour
        $forecastCacheKey = "forecast_{$location->latitude}_{$location->longitude}";
        $forecastData = Cache::remember($forecastCacheKey, 3600, function () use ($location, $weather) {
            $forecast = [];
            $dailyData = array_filter($weather['properties']['timeseries'], function ($entry) {
                return substr($entry['time'], 11, 8) === '00:00:00'; // Midnight entries
            });
            $dailyData = array_values($dailyData);
            for ($i = 0; $i < min(10, count($dailyData)); $i++) {
                $day = $dailyData[$i];
                $forecast[] = [
                    'date' => $day['time'],
                    'temperature' => $day['data']['instant']['details']['air_temperature'] ?? null,
                    'wind_speed' => $day['data']['instant']['details']['wind_speed'] ?? null,
                    'humidity' => $day['data']['instant']['details']['relative_humidity'] ?? null,
                ];
            }
            return $forecast;
        });

        // Cache sunrise/sunset data for 1 hour
        $sunCacheKey = "sun_{$location->latitude}_{$location->longitude}";
        $sunData = Cache::remember($sunCacheKey, 3600, function () use ($location) {
            $sunriseSunset = [];
            for ($i = 0; $i < 10; $i++) {
                $date = now()->addDays($i)->toDateString();
                $sunResponse = $this->weatherService->getSunriseSunset($location->latitude, $location->longitude, $date);
                $sunriseSunset[$date] = [
                    'sunrise' => $sunResponse['sunrise'],
                    'sunset' => $sunResponse['sunset'],
                ];
            }
            return $sunriseSunset;
        });

        $marine = null;
        if ($location->type === 'Marine') {
            $marineCacheKey = "marine_{$location->latitude}_{$location->longitude}";
            $marineResponse = Cache::remember($marineCacheKey, 3600, function () use ($location) {
                return $this->weatherService->getMarineForecast($location->latitude, $location->longitude);
            });
            if ($marineResponse) {
                $marine = [
                    'wave_height' => $marineResponse['hourly']['wave_height'][0] ?? null,
                    'wave_direction' => $marineResponse['hourly']['wave_direction'][0] ?? null,
                    'wave_period' => $marineResponse['hourly']['wave_period'][0] ?? null,
                    'wind_wave_height' => $marineResponse['hourly']['wind_wave_height'][0] ?? null,
                    'swell_wave_height' => $marineResponse['hourly']['swell_wave_height'][0] ?? null,
                    'swell_wave_direction' => $marineResponse['hourly']['swell_wave_direction'][0] ?? null,
                    'swell_wave_period' => $marineResponse['hourly']['swell_wave_period'][0] ?? null,
                    'water_temperature' => $marineResponse['hourly']['water_temperature'][0] ?? null,
                ];
            }
        }

        $weatherData = [
            'current' => $currentWeather,
            'forecast' => $forecastData,
            'sun' => $sunData,
            'marine' => $marine,
            'type' => $location->type,
            'altitude' => $location->altitude ?? 0,
        ];

        return view('location', compact('location', 'weatherData'));
    }
}