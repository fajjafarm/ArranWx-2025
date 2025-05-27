<?php
namespace App\Http\Controllers;

use App\Services\WeatherService;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class WeatherController extends Controller
{
    protected $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    /**
     * Convert degrees to cardinal direction
     *
     * @param float|null $degrees
     * @return string
     */
    public function degreesToCardinal($degrees)
    {
        if ($degrees === null) {
            return 'N/A';
        }
        $directions = ['N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE', 'S', 'SSW', 'SW', 'WSW', 'W', 'WNW', 'NW', 'NNW'];
        $index = round($degrees / 22.5) % 16;
        return $directions[$index];
    }

    public function warnings(Request $request)
    {
        $data = [
            'metOffice' => [
                'class' => 'weather-no-warning',
                'text' => 'No Warnings',
                'updated' => Carbon::now('GMT')->format('d M Y H:i')
            ],
            'meteogram' => [
                'imageUrl' => 'https://via.placeholder.com/580x100?text=Meteogram+Placeholder',
                'updated' => Carbon::now('GMT')->format('d M Y H:i')
            ],
            'sepaFlood' => [
                'class' => 'weather-no-warning',
                'text' => 'No Warnings',
                'updated' => Carbon::now('GMT')->format('d M Y H:i')
            ]
        ];

        return response()->json($data);
    }

    public function index()
    {
        $brodickArdrossanStatus = [
            'class' => 'status-red',
            'text' => 'No Service',
            'updated' => Carbon::create(2025, 1, 13, 5, 37, 0, 'GMT')->format('d M Y H:i')
        ];

        $brodickTroonStatus = [
            'class' => 'status-amber',
            'text' => 'Disrupted',
            'updated' => Carbon::create(2025, 3, 12, 12, 0, 0, 'GMT')->format('d M Y H:i')
        ];

        $lochranzaClaonaigStatus = [
            'class' => 'status-green',
            'text' => 'Normal',
            'updated' => Carbon::create(2025, 3, 10, 11, 49, 0, 'GMT')->format('d M Y H:i')
        ];

        $metOfficeWarning = [
            'class' => 'weather-no-warning',
            'text' => 'No Warnings',
            'updated' => Carbon::now('GMT')->format('d M Y H:i')
        ];

        $meteogram = [
            'imageUrl' => 'https://via.placeholder.com/580x100?text=Meteogram+Placeholder',
            'updated' => Carbon::now('GMT')->format('d M Y H:i')
        ];

        $sepaFloodWarning = [
            'class' => 'weather-no-warning',
            'text' => 'No Warnings',
            'updated' => Carbon::now('GMT')->format('d M Y H:i')
        ];

        $locations = Location::all();
        $weatherData = [];

        foreach ($locations as $location) {
            Log::info("Fetching weather data for {$location->name}");
            $weather = $this->weatherService->getWeather($location->latitude, $location->longitude);
            Log::info("Raw weather data for {$location->name}", ['data' => $weather]);
            $weatherDetails = [];
            if (isset($weather['properties']['timeseries'][0]['data']['instant']['details'])) {
                $weatherDetails = $weather['properties']['timeseries'][0]['data']['instant']['details'];
            }

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
                'wind_direction' => $this->degreesToCardinal($weatherDetails['wind_from_direction'] ?? null),
                'wind_from_direction' => $weatherDetails['wind_from_direction'] ?? null,
                'marine' => $marine,
                'type' => $location->type,
                'altitude' => $location->altitude ?? 0,
            ];
        }

        Log::info("Weather data sent to dashboard view", $weatherData);
        return view('dashboard', compact('weatherData', 'locations', 'brodickArdrossanStatus', 'brodickTroonStatus', 'lochranzaClaonaigStatus',
                'metOfficeWeather', 'metogramWeather', 'sepaFloodStatus'));
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
            $previousPressure = null;

            foreach ($timeseries as $entry) {
                $time = Carbon::parse($entry['time']);
                if ($time->minute === 0 && $time->hour % 2 === 0) {
                    $date = $time->toDateString();
                    $details = $entry['data']['instant']['details'];
                    $next1Hour = $entry['data']['next_1_hours'] ?? ['summary' => ['symbol_code' => 'N/A'], 'details' => ['precipitation_amount' => 0]];
                    if ($next1Hour['summary']['symbol_code'] == 'N/A') {
                        $next1Hour = $entry['data']['next_6_hours'] ?? ['summary' => ['symbol_code' => 'N/A'], 'details' => ['precipitation_amount' => 0]];
                    }

                    $windSpeed = $details['wind_speed'] ?? 0;
                    $cloudCover = $details['cloud_area_fraction'] ?? 0;
                    $pressure = $details['air_pressure_at_sea_level'] ?? null;
                    $altitude = $location->altitude ?? 0;

                    $gustFactor = $location->type === 'Hill' ? 1.6 : 1.5;
                    if ($cloudCover > 75) {
                        $gustFactor += 0.2;
                    } elseif ($cloudCover < 25) {
                        $gustFactor -= 0.1;
                    }
                    if ($previousPressure !== null && $pressure !== null) {
                        $pressureChange = $previousPressure - $pressure;
                        if ($pressureChange > 1) {
                            $gustFactor += 0.2;
                        } elseif ($pressureChange < -1) {
                            $gustFactor -= 0.1;
                        }
                    }
                    $previousPressure = $pressure;
                    $altitudeMultiplier = $location->type === 'Hill' ? (1 + ($altitude / 100) * 0.015) : 1;
                    $windGust = $details['wind_speed_of_gust'] ?? ($windSpeed * $gustFactor * $altitudeMultiplier);

                    $hourlyData[$date][] = [
                        'time' => $time->format('H:i'),
                        'temperature' => $details['air_temperature'] ?? null,
                        'precipitation' => $next1Hour['details']['precipitation_amount'] ?? 0,
                        'condition' => $next1Hour['summary']['symbol_code'] ?? 'N/A',
                        'wind_speed' => $windSpeed,
                        'wind_gust' => round($windGust, 1),
                        'wind_direction' => $this->degreesToCardinal($details['wind_from_direction'] ?? null),
                        'wind_from_direction' => $details['wind_from_direction'] ?? null,
                        'pressure' => $pressure,
                    ];
                    Log::info("Hourly data entry for {$location->name}", ['entry' => $hourlyData[$date][count($hourlyData[$date]) - 1]]);
                }
            }
            $hourlyData = array_slice($hourlyData, 0, 10, true);
            Log::info("Processed 2-hourly data for {$location->name}", ['hourly' => $hourlyData]);
        }

        Log::info("Fetching sun and moon data for {$location->name}");
        $sunMoonData = [];
        for ($i = 0; $i < 10; $i++) {
            $date = Carbon::now()->addDays($i)->toDateString();
            $sunMoon = $this->weatherService->getSunriseSunset($location->latitude, $location->longitude, $date);
            $sunMoonData[$date] = [
                'sunrise' => $sunMoon['sunrise'],
                'sunset' => $sunMoon['sunset'],
                'moonrise' => $sunMoon['moonrise'],
                'moonset' => $sunMoon['moonset'],
                'moonphase' => $sunMoon['moonphase'] ?? null
            ];
        }
        Log::info("Sun and moon data for {$location->name}", ['sun_moon' => $sunMoonData]);

        $marine = null;
        $marineForecast = [];
        $marineHourly = [];
        $marineApiUrl = null;
        if ($location->type === 'Marine') {
            Log::info("Fetching marine data for {$location->name}", ['lat' => $location->latitude, 'lon' => $location->longitude]);
            try {
                $marineApiUrl = 'https://marine-api.open-meteo.com/v1/marine?' . http_build_query([
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude,
                    'hourly' => 'wave_height,sea_surface_temperature,sea_level_height_msl,wave_direction,wave_period,wind_wave_height',
                    'daily' => 'wave_height_max',
                    'wind_speed_unit' => 'mph',
                    'timezone' => 'GMT'
                ]);
                $marineResponse = Http::get($marineApiUrl)->json();

                Log::info("Raw marine data for {$location->name}", ['url' => $marineApiUrl, 'data' => $marineResponse]);

                if ($marineResponse && isset($marineResponse['hourly'], $marineResponse['daily'])) {
                    $currentHourIndex = 18; // 18:00 GMT (19:00 BST, May 27, 2025)
                    $marine = [
                        'wave_height' => $marineResponse['current']['wave_height'] ?? null,
                        'wave_direction' => $marineResponse['hourly']['wave_direction'][$currentHourIndex] ?? null,
                        'wave_period' => $marineResponse['hourly']['wave_period'][$currentHourIndex] ?? null,
                        'wind_wave_height' => $marineResponse['hourly']['wind_wave_height'][$currentHourIndex] ?? null,
                        'swell_wave_height' => null,
                        'swell_wave_direction' => null,
                        'swell_wave_period' => null,
                        'sea_surface_temperature' => $marineResponse['hourly']['sea_surface_temperature'][$currentHourIndex] ?? null,
                    ];

                    $marineForecast = array_map(function ($time, $wave_height_max) {
                        return [
                            'date' => $time,
                            'wave_height_max' => $wave_height_max,
                        ];
                    }, array_slice($marineResponse['daily']['time'], 0, 3), array_slice($marineResponse['daily']['wave_height_max'], 0, 3));

                    $marineHourly = array_map(function ($time, $wave_height, $sea_surface_temperature, $sea_level_height_msl, $wave_direction, $wave_period, $wind_wave_height) {
                        return [
                            'time' => $time,
                            'wave_height' => $wave_height,
                            'sea_surface_temperature' => $sea_surface_temperature,
                            'sea_level_height_msl' => $sea_level_height_msl,
                            'wave_direction' => $wave_direction,
                            'wave_period' => $wave_period,
                            'wind_wave_height' => $wind_wave_height,
                        ];
                    }, array_slice($marineResponse['hourly']['time'], 0, 72), 
                       array_slice($marineResponse['hourly']['wave_height'], 0, 72), 
                       array_slice($marineResponse['hourly']['sea_surface_temperature'], 0, 72), 
                       array_slice($marineResponse['hourly']['sea_level_height_msl'], 0, 72), 
                       array_slice($marineResponse['hourly']['wave_direction'], 0, 72), 
                       array_slice($marineResponse['hourly']['wave_period'], 0, 72), 
                       array_slice($marineResponse['hourly']['wind_wave_height'], 0, 72));
                } else {
                    Log::error("Invalid marine response for {$location->name}", ['url' => $marineApiUrl, 'response' => $marineResponse]);
                }
            } catch (\Exception $e) {
                Log::error("Failed to fetch marine data for {$location->name}", ['url' => $marineApiUrl, 'error' => $e->getMessage()]);
            }
        }

        $weatherData = [
            'current' => $currentWeather,
            'hourly' => $hourlyData,
            'sun' => $sunMoonData,
            'marine' => $marine,
            'marine_forecast' => $marineForecast,
            'marine_hourly' => $marineHourly,
            'type' => $location->type,
            'altitude' => $location->altitude ?? 0,
            'marine_api_url' => $marineApiUrl
        ];

        Log::info("Weather data sent to view for {$location->name}", ['weatherData' => $weatherData]);

        return view('location', [
            'location' => $location,
            'weatherData' => $weatherData,
            'controller' => $this
        ]);
    }
}