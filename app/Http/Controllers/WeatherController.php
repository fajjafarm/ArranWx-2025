<?php
namespace App\Http\Controllers;

use App\Services\WeatherService;
use App\Models\Location;
use Illuminate\Http\Request;

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
            $weather = $this->weatherService->getWeather($location->latitude, $location->longitude);
            $weatherDetails = $weather['properties']['timeseries'][0]['data']['instant']['details'];

            $marine = null;
            if ($location->type === 'Marine') {
                $marineResponse = $this->weatherService->getMarineForecast($location->latitude, $location->longitude);
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
                'altitude' => $location->altitude,
            ];
        }

        return view('dashboard', compact('weatherData', 'locations'));
    }

    public function show($name)
    {
        $location = Location::where('name', $name)->firstOrFail();
        $weather = $this->weatherService->getWeather($location->latitude, $location->longitude);
        $weatherDetails = $weather['properties']['timeseries'][0]['data']['instant']['details'];

        $marine = null;
        if ($location->type === 'Marine') {
            $marineResponse = $this->weatherService->getMarineForecast($location->latitude, $location->longitude);
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
            'weather' => $weatherDetails,
            'marine' => $marine,
            'type' => $location->type,
            'altitude' => $location->altitude,
        ];

        return view('location', compact('location', 'weatherData'));
    }
}