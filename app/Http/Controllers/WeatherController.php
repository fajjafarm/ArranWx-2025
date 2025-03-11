<?php
namespace App\Http\Controllers;

use App\Services\WeatherService;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
            Log::info("Fetching weather data for {$location->name}");
            $weather = $this->weatherService->getWeather($location->latitude, $location->longitude);
            Log::info("Raw weather data for {$location->name}", ['data' => $weather]);
            $weatherDetails = isset($weather['properties']['timeseries'][0]['data']['instant']['details'])
                ? $weather['properties']['timeseries'][0]['data']['instant']['details']
                : [];

            $marine = null;
            if ($location->type === 'Marine') {
                Log::info("Fetching marine data for {$location->name}");
                $marineResponse = $this->weatherService->getMarineForecast($location->latitude, $location->longitude);
                Log::info("Raw marine data for {$location->name}", ['data' => $marineResponse]);
                if ($marineResponse) {
                    $marine = [
                        'wave_height' => $marineResponse['hourly']['wave_height'][0] ?? null,
                        'wave_direction' => $marineResponse['hourly']['wave_direction'][0] ?? null,
                        'wave_period' => $marineResponse['hourly']['wave_period'][0] ?? null,
                        'wind_wave_height' => $marineResponse['hourly']['wind_wave_height'][0] ?? null,
                        'swell_wave_height' => $marineResponse['hourly']['swell_wave_height'][0] ?? null,
                        'swell_wave_direction' => $marineResponse['hourly']['swell_wave_direction'][0] ?? null,
                        'swell_wave_period' => $marineResponse['hourly']['swell_wave_period'][0] ?? null,
                        'sea_surface_temperature' => $marineResponse['hourly']['sea_surface_temperature'][0] ?? null,
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

        Log::info("Weather data sent to dashboard view", $weatherData);
        return view('dashboard', compact('weatherData', 'locations'));
    }

    public function show($name)
    {
        $location = Location::where('name', $name)->firstOrFail();

        Log::info("Fetching weather data for {$location->name}", ['lat' => $location->latitude, 'lon' => $location->longitude]);
        $weather = $this->weatherService->getWeather($location->latitude, $location->longitude);
        Log::info("Raw weather data for {$location->name}", ['data' => $weather]);

        // Handle missing or invalid weather data
        if (!isset($weather['properties']) || !isset($weather['properties']['timeseries']) || empty($weather['properties']['timeseries'])) {
            Log::warning("No valid timeseries data returned for {$location->name}", ['response' => $weather]);
            $currentWeather = [];
            $forecastData = [];
        } else {
            $currentWeather = isset($weather['properties']['timeseries'][0]['data']['instant']['details'])
                ? $weather['properties']['timeseries'][0]['data']['instant']['details']
                : [];

            Log::info("Processing forecast data for {$location->name}");
            $forecastData = [];
            $dailyData = array_filter($weather['properties']['timeseries'], function ($entry) {
                return substr($entry['time'], 11, 8) === '00:00:00'; // Midnight entries
            });
            $dailyData = array_values($dailyData);
            for ($i = 0; $i < min(10, count($dailyData)); $i++) {
                $day = $dailyData[$i];
                $forecastData[] = [
                    'date' => $day['time'],
                    'temperature' => $day['data']['instant']['details']['air_temperature'] ?? null,
                    'wind_speed' => $day['data']['instant']['details']['wind_speed'] ?? null,
                    'humidity' => $day['data']['instant']['details']['relative_humidity'] ?? null,
                ];
            }
            Log::info("Processed forecast data for {$location->name}", ['forecast' => $forecastData]);
        }

        Log::info("Fetching sunrise/sunset data for {$location->name}");
        $sunData = [];
        for ($i = 0; $i < 10; $i++) {
            $date = now()->addDays($i)->toDateString();
            $sunResponse = $this->weatherService->getSunriseSunset($location->latitude, $location->longitude, $date);
            $sunData[$date] = [
                'sunrise' => $sunResponse['sunrise'],
                'sunset' => $sunResponse['sunset'],
            ];
        }
        Log::info("Sunrise/sunset data for {$location->name}", ['sun' => $sunData]);

        $marine = null;
        if ($location->type === 'Marine') {
            Log::info("Fetching marine data for {$location->name}");
            $marineResponse = $this->weatherService->getMarineForecast($location->latitude, $location->longitude);
            Log::info("Raw marine data for {$location->name}", ['data' => $marineResponse]);
            if ($marineResponse) {
                $marine = [
                    'wave_height' => $marineResponse['hourly']['wave_height'][0] ?? null,
                    'wave_direction' => $marineResponse['hourly']['wave_direction'][0] ?? null,
                    'wave_period' => $marineResponse['hourly']['wave_period'][0] ?? null,
                    'wind_wave_height' => $marineResponse['hourly']['wind_wave_height'][0] ?? null,
                    'swell_wave_height' => $marineResponse['hourly']['swell_wave_height'][0] ?? null,
                    'swell_wave_direction' => $marineResponse['hourly']['swell_wave_direction'][0] ?? null,
                    'swell_wave_period' => $marineResponse['hourly']['swell_wave_period'][0] ?? null,
                    'sea_surface_temperature' => $marineResponse['hourly']['sea_surface_temperature'][0] ?? null,
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

        Log::info("Weather data sent to view for {$location->name}", $weatherData);

        return view('location', compact('location', 'weatherData'));
    }
}