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

        if (!isset($weather['properties']) || !isset($weather['properties']['timeseries']) || empty($weather['properties']['timeseries'])) {
            Log::warning("No valid timeseries data returned for {$location->name}", ['response' => $weather]);
            $currentWeather = [];
            $hourlyData = [];
        } else {
            $currentWeather = isset($weather['properties']['timeseries'][0]['data']['instant']['details'])
                ? $weather['properties']['timeseries'][0]['data']['instant']['details']
                : [];

            Log::info("Processing 2-hourly data for {$location->name}");
            $hourlyData = [];
            $timeseries = $weather['properties']['timeseries'];
            foreach ($timeseries as $entry) {
                $time = \Carbon\Carbon::parse($entry['time']);
                // Filter to every 2 hours (00:00, 02:00, 04:00, etc.)
                if ($time->minute === 0 && $time->hour % 2 === 0) {
                    $date = $time->toDateString();
                    $details = $entry['data']['instant']['details'];
                    $next1Hour = $entry['data']['next_1_hours'] ?? ['summary' => ['symbol_code' => 'N/A'], 'details' => ['precipitation_amount' => 0]];
                    $hourlyData[$date][] = [
                        'time' => $time->format('H:i'),
                        'temperature' => $details['air_temperature'] ?? null,
                        'precipitation' => $next1Hour['details']['precipitation_amount'] ?? 0,
                        'condition' => $next1Hour['summary']['symbol_code'] ?? 'N/A',
                        'wind_speed' => $details['wind_speed'] ?? null,
                        'wind_gust' => $details['wind_speed_of_gust'] ?? null, // Yr.no provides this
                        'pressure' => $details['air_pressure_at_sea_level'] ?? null,
                    ];
                }
            }
            // Limit to 10 days
            $hourlyData = array_slice($hourlyData, 0, 10, true);
            Log::info("Processed 2-hourly data for {$location->name}", ['hourly' => $hourlyData]);
        }

        Log::info("Fetching sun and moon data for {$location->name}");
        $sunMoonData = [];
        for ($i = 0; $i < 10; $i++) {
            $date = now()->addDays($i)->toDateString();
            $sunMoon = $this->weatherService->getSunriseSunset($location->latitude, $location->longitude, $date);
            $sunMoonData[$date] = [
                'sunrise' => $sunMoon['sunrise'],
                'sunset' => $sunMoon['sunset'],
                'moonrise' => $sunMoon['moonrise'],
                'moonset' => $sunMoon['moonset'],
            ];
        }
        Log::info("Sun and moon data for {$location->name}", ['sun_moon' => $sunMoonData]);

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
            'hourly' => $hourlyData, // Replacing 'forecast' with 'hourly'
            'sun_moon' => $sunMoonData, // Replacing 'sun' with 'sun_moon'
            'marine' => $marine,
            'type' => $location->type,
            'altitude' => $location->altitude ?? 0,
        ];

        Log::info("Weather data sent to view for {$location->name}", $weatherData);

        return view('location', compact('location', 'weatherData'));
    }
}