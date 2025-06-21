<?php

namespace App\Http\Controllers;

use App\Services\WeatherService;
use App\Models\Location;
use App\Models\ApiCache;
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
     * @param float|null $degrees
     * @return string
     */
    public function degreesToCardinal($degrees)
    {
        if (!is_numeric($degrees)) {
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
                'updated' => Carbon::now('UTC')->format('Y-m-d H:i')
            ],
            'meteogram' => [
                'imageUrl' => 'https://via.placeholder.com/580x100?text=Meteogram+Placeholder',
                'updated' => Carbon::now('UTC')->format('Y-m-d H:i')
            ],
            'sepaFlood' => [
                'class' => 'weather-no-warning',
                'text' => 'No Warnings',
                'updated' => Carbon::now('UTC')->format('Y-m-d H:i')
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
            'updated' => Carbon::create(2025, 3, 10, 11, 47, 0, 'GMT')->format('d M Y H:i')
        ];

        $metOfficeWarning = [
            'class' => 'weather-no-warning',
            'text' => 'No Warnings',
            'updated' => Carbon::now('UTC')->format('Y-m-d H:i')
        ];

        $meteogram = [
            'imageUrl' => 'https://via.placeholder.com/580x100?text=Meteogram+Placeholder',
            'updated' => Carbon::now('UTC')->format('Y-m-d H:i')
        ];

        $sepaFloodWarning = [
            'class' => 'weather-no-warning',
            'text' => 'No Warnings',
            'updated' => Carbon::now('UTC')->format('Y-m-d H:i')
        ];

        // Cache locations for 24 hours in database
        $locations = ApiCache::getCached('locations') ?? Location::all()->toArray();
        if (!ApiCache::getCached('locations')) {
            ApiCache::setCached('locations', $locations, 24 * 60); // 24 hours
        }

        $weatherData = [];

        foreach ($locations as $location) {
            $location = (object) $location; // Convert array to object for consistency
            Log::info("Fetching weather data for {$location->name}");

            // Cache weather data
            $weatherCacheKey = "weather_{$location->latitude}_{$location->longitude}";
            $weather = ApiCache::getCached($weatherCacheKey);
            if (!$weather) {
                $weather = $this->weatherService->getWeather($location->latitude, $location->longitude);
                if (!empty($weather)) {
                    ApiCache::setCached($weatherCacheKey, $weather, 60); // 1 hour
                }
            }
            Log::info("Raw weather data for {$location->name}", ['data' => $weather]);
            $weatherDetails = isset($weather['properties']['timeseries'][0]['data']['instant']['details'])
                ? $weather['properties']['timeseries'][0]['data']['instant']['details']
                : [];

            $marine = null;
            if ($location->type === 'Marine') {
                Log::info("Fetching marine data for {$location->name}");
                $marineCacheKey = "marine_{$location->latitude}_{$location->longitude}";
                $marineResponse = ApiCache::getCached($marineCacheKey);
                if (!$marineResponse) {
                    $marineResponse = $this->weatherService->getMarineForecast($location->latitude, $location->longitude);
                    if (!empty($marineResponse)) {
                        ApiCache::setCached($marineCacheKey, $marineResponse, 60); // 1 hour
                    }
                }
                Log::info("Raw marine data for {$location->name}", ['data' => $marineResponse]);
                if ($marineResponse) {
                    $marine = [
                        'wave_height' => $marineResponse['hourly']['wave_height'][0] ?? null,
                        'wave_direction' => $marineResponse['hourly']['wave_direction'][0] ?? null,
                        'wave_period' => $marineResponse['hourly']['wave_period'][0] ?? null,
                        'wind_wave_height' => $marineResponse['hourly']['wind_wave_height'][0] ?? null,
                        'swell_wave_height' => $marineResponse['hourly']['swell_wave_height'][0] ?? null,
                        'swell_wave_direction' => null,
                        'swell_wave_period' => null,
                        'sea_surface_temperature' => $marineResponse['hourly']['sea_surface_temperature'][0] ?? null,
                    ];
                }
            }

            $weatherData[$location->name] = [
                'weather' => $weatherDetails,
                'wind_direction' => $this->degreesToCardinal($weatherDetails['wind_from_direction'] ?? null),
                'wind_from_direction_degrees' => $weatherDetails['wind_from_direction'] ?? null,
                'marine' => $marine,
                'type' => $location->type,
                'altitude' => $location->altitude ?? 0,
            ];
        }

        Log::info("Weather data sent to dashboard view", ['weather_data' => $weatherData]);
        return view('dashboard', compact('weatherData', 'locations', 'brodickArdrossanStatus', 'brodickTroonStatus', 'lochranzaClaonaigStatus',
            'metOfficeWarning', 'meteogram', 'sepaFloodWarning'));
    }

    public function show(Request $request, $name)
    {
        $location = Location::where('name', $name)->firstOrFail();

        // Determine expected location type based on route
        $routeUri = $request->route()->uri();
        $isMarineRoute = str_contains($routeUri, 'marine');
        $expectedType = $isMarineRoute ? ['Marine'] : ['Village', 'Hill'];

        if (!in_array($location->type, $expectedType)) {
            Log::warning("Location type mismatch for {$location->name}", [
                'location_type' => $location->type,
                'expected_types' => $expectedType,
                'route_uri' => $routeUri
            ]);
            abort(404, 'Location type does not match the requested forecast type.');
        }

        Log::info("Fetching weather data for {$location->name}", ['lat' => $location->latitude, 'lon' => $location->longitude]);
        
        // Cache weather data
        $weatherCacheKey = "weather_{$location->latitude}_{$location->longitude}";
        $weather = ApiCache::getCached($weatherCacheKey);
        if (!$weather) {
            $weather = $this->weatherService->getWeather($location->latitude, $location->longitude);
            if (!empty($weather)) {
                ApiCache::setCached($weatherCacheKey, $weather, 60); // 1 hour
            }
        }
        Log::info("Raw weather data for {$location->name}", ['data' => $weather]);

        if (!isset($weather['properties']) || !isset($weather['properties']['timeseries']) || empty($weather['properties']['timeseries'])) {
            Log::warning("No valid timeseries data returned for {$location->name}", ['response' => $weather]);
            $currentWeather = [];
            $hourlyData = [];
        } else {
            // Log timeseries duration
            $timeseries = $weather['properties']['timeseries'];
            $firstTime = Carbon::parse($timeseries[0]['time'])->setTimezone('Europe/London');
            $lastTime = Carbon::parse(end($timeseries)['time'])->setTimezone('Europe/London');
            $daysAvailable = $firstTime->diffInDays($lastTime);
            Log::info("Timeseries data for {$location->name}", ['days' => $daysAvailable, 'entries' => count($timeseries)]);

            $currentWeather = isset($timeseries[0]['data']['instant']['details'])
                ? $timeseries[0]['data']['instant']['details']
                : [];

            Log::info("Processing 2-hourly data for {$location->name}");
            $hourlyData = [];
            $previousPressure = null;

            $conditionsMap = [
                'clearsky' => 'clearsky_day',
                'fair' => 'fair_day',
                'partlycloudy' => 'partlycloudy_day',
                'cloudy' => 'cloudy',
                'fog' => 'fog',
                'lightrain' => 'lightrain',
                'rain' => 'rain',
                'heavyrain' => 'heavyrain',
                'lightrainshowers' => 'lightrainshowers_day',
                'rainshowers' => 'rainshowers_day',
                'heavyrainshowers' => 'heavyrainshowers_day',
                'lightsnow' => 'lightsnow',
                'snow' => 'snow',
                'heavysnow' => 'heavysnow',
                'lightsnowshowers' => 'lightsnowshowers_day',
                'snowshowers' => 'snowshowers_day',
                'heavysnowshowers' => 'heavysnowshowers_day',
                'sleet' => 'sleet',
                'lightsleet' => 'sleet',
                'heavysleet' => 'sleet',
                'thunder' => 'thunder',
                'rainandthunder' => 'rainandthunder',
                'snowandthunder' => 'snowandthunder'
            ];

            foreach ($timeseries as $entry) {
                if (!isset($entry['time'], $entry['data']['instant']['details'])) {
                    Log::warning("Skipping invalid timeseries entry for {$location->name}", ['entry' => $entry]);
                    continue;
                }
                $time = Carbon::parse($entry['time'])->setTimezone('Europe/London');
                if ($time->minute === 0 && $time->hour % 2 === 0 && $time->diffInDays(Carbon::now('Europe/London')) <= 10) {
                    $date = $time->toDateString();
                    $details = $entry['data']['instant']['details'];
                    $next1Hour = $entry['data']['next_1_hours'] ?? ['summary' => ['symbol_code' => 'N/A'], 'details' => ['precipitation_amount' => 0]];
                    if ($next1Hour['summary']['symbol_code'] === 'N/A') {
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

                    $symbolCode = $next1Hour['summary']['symbol_code'] ?? 'N/A';
                    $condition = $conditionsMap[str_replace(['_day', '_night'], '', $symbolCode)] ?? 'unknown';
                    if ($time->hour >= 20 || $time->hour <= 1) {
                        $condition = str_replace('_day', '_night', $condition);
                    }

                    $hourlyData[$date][] = [
                        'time' => $time->format('H:i'),
                        'temperature' => $details['air_temperature'] ?? null,
                        'precipitation' => $next1Hour['details']['precipitation_amount'] ?? 0,
                        'condition' => $condition,
                        'wind_speed' => $windSpeed,
                        'wind_gust' => round($windGust, 1),
                        'wind_direction' => $this->degreesToCardinal($details['wind_from_direction'] ?? null),
                        'wind_from_direction_degrees' => $details['wind_from_direction'] ?? null,
                        'pressure' => $pressure,
                    ];
                }
            }
            $hourlyData = array_slice($hourlyData, 0, 10, true); // 10 days
            Log::info("Processed 2-hourly data for {$location->name}", ['days' => count($hourlyData), 'dates' => array_keys($hourlyData)]);
        }

        Log::info("Fetching sun and moon data for {$location->name}");
        $sunMoonData = [];
        for ($i = 0; $i < 10; $i++) {
            $date = Carbon::today()->addDays($i)->toDateString();
            $sunMoonCacheKey = "sun_moon_{$location->latitude}_{$location->longitude}_{$date}";
            $sunMoon = ApiCache::getCached($sunMoonCacheKey);
            if (!$sunMoon) {
                $sunMoon = $this->weatherService->getSunriseSunset($location->latitude, $location->longitude, $date);
                if (!empty($sunMoon)) {
                    ApiCache::setCached($sunMoonCacheKey, $sunMoon, 24 * 60); // 24 hours for sun/moon (data doesn't change frequently)
                }
            }
            $moonPhase = isset($sunMoon['moonphase']) && is_numeric($sunMoon['moonphase']) ? $sunMoon['moonphase'] : null;
            if ($moonPhase !== null && $moonPhase > 1) {
                $moonPhase = $moonPhase / 360;
            }
            $sunMoonData[$date] = [
                'sunrise' => isset($sunMoon['sunrise']) && $sunMoon['sunrise'] !== 'N/A' ? $sunMoon['sunrise'] : null,
                'sunset' => isset($sunMoon['sunset']) && $sunMoon['sunset'] !== 'N/A' ? $sunMoon['sunset'] : null,
                'moonrise' => isset($sunMoon['moonrise']) && $sunMoon['moonrise'] !== 'N/A' ? $sunMoon['moonrise'] : null,
                'moonset' => isset($sunMoon['moonset']) && $sunMoon['moonset'] !== 'N/A' ? $sunMoon['moonset'] : null,
                'moonphase' => $moonPhase
            ];
            if (in_array(null, [$sunMoonData[$date]['sunrise'], $sunMoonData[$date]['sunset'], $sunMoonData[$date]['moonrise'], $sunMoonData[$date]['moonset']], true)) {
                Log::warning("Missing or invalid sun/moon data for {$location->name} on {$date}", ['sun_moon' => $sunMoon]);
            }
        }
        Log::info("Sun and moon data for {$location->name}", ['sun_moon' => $sunMoonData]);

        $marine = null;
        $marineForecast = [];
        $marineHourly = [];
        $marineApiUrl = null;
        $dailyMarineData = [];
        if ($location->type === 'Marine') {
            Log::info("Fetching marine data for {$location->name}", ['lat' => $location->latitude, 'lon' => $location->longitude]);
            try {
                $currentDate = Carbon::now('Europe/London');
                $isBst = $currentDate->isDST();
                $apiTimezone = $isBst ? 'Europe/London' : 'GMT';

                $marineApiUrl = 'https://marine-api.open-meteo.com/v1/marine?' . http_build_query([
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude,
                    'hourly' => 'wave_height,sea_surface_temperature,sea_level_height_msl,wave_direction,wave_period,wind_wave_height,swell_wave_height',
                    'daily' => 'wave_height_max,wind_wave_height_max,swell_wave_height_max,wave_direction_dominant,wind_wave_direction_dominant',
                    'wind_speed_unit' => 'mph',
                    'timezone' => $apiTimezone,
                    'past_days' => 0,
                    'forecast_days' => 10 // Extended to 10 days
                ]);

                $marineCacheKey = "marine_{$location->latitude}_{$location->longitude}";
                $marineResponse = ApiCache::getCached($marineCacheKey);
                if (!$marineResponse) {
                    $marineResponse = Http::get($marineApiUrl)->json();
                    if (!empty($marineResponse)) {
                        ApiCache::setCached($marineCacheKey, $marineResponse, 60); // 1 hour
                    }
                }

                Log::info("Raw marine data for {$location->name}", ['url' => $marineApiUrl, 'data' => $marineResponse]);

                if (!isset($marineResponse['hourly'], $marineResponse['daily'])) {
                    Log::warning("Invalid marine response for {$location->name}, using fallback JSON", ['response' => $marineResponse]);
                    $marineResponse = json_decode('{"latitude":54.541664,"longitude":10.2083435,"current":{"time":"2025-06-12T18:00","interval":3600,"wave_height":0.24,"swell_wave_height":0.10,"sea_level_height_msl":-0.54,"sea_surface_temperature":14.3,"wave_period":2.45,"wave_direction":232},"hourly":{"time":["2025-06-12T00:00",...,"2025-06-21T23:00"],"wave_height":[0.22,...,0.20],"sea_surface_temperature":[14.2,...,15.4],"sea_level_height_msl":[-0.20,...,-0.29],"wave_direction":[222,...,275],"wave_period":[2.10,...,2.25],"wind_wave_height":[0.20,...,0.14],"swell_wave_height":[0.10,...,0.14]},"daily":{"time":["2025-06-12","2025-06-13","2025-06-14","2025-06-15","2025-06-16","2025-06-17","2025-06-18","2025-06-19","2025-06-20","2025-06-21"],"wave_height_max":[0.60,0.44,0.38,0.62,0.32,0.26,0.36,0.40,0.45,0.50],"wind_wave_height_max":[0.58,0.42,0.34,0.60,0.32,0.24,0.36,0.38,0.43,0.48],"swell_wave_height_max":[0.16,0.22,0.26,0.20,0.14,0.14,0.14,0.18,0.20,0.22],"wave_direction_dominant":[206,284,283,266,259,163,262,270,275,280],"wind_wave_direction_dominant":[204,280,260,264,253,224,253,265,270,275]}}', true);
                }

                if ($marineResponse && isset($marineResponse['hourly'], $marineResponse['daily'])) {
                    $currentHourIndex = $currentDate->hour;
                    if ($isBst) {
                        $currentHourIndex -= 1; // Adjust for BST
                    }
                    $marine = [
                        'wave_height' => $marineResponse['current']['wave_height'] ?? null,
                        'wave_direction' => $marineResponse['hourly']['wave_direction'][$currentHourIndex] ?? null,
                        'wave_period' => $marineResponse['hourly']['wave_period'][$currentHourIndex] ?? null,
                        'wind_wave_height' => $marineResponse['hourly']['wind_wave_height'][$currentHourIndex] ?? null,
                        'swell_wave_height' => $marineResponse['hourly']['swell_wave_height'][$currentHourIndex] ?? null,
                        'swell_wave_direction' => null,
                        'swell_wave_period' => null,
                        'sea_surface_temperature' => $marineResponse['hourly']['sea_surface_temperature'][$currentHourIndex] ?? null,
                        'sea_level_height_msl' => $marineResponse['hourly']['sea_level_height_msl'][$currentHourIndex] ?? null,
                    ];

                    $marineForecast = array_map(function ($time, $wave_height_max) {
                        return [
                            'date' => $time,
                            'wave_height_max' => $wave_height_max,
                        ];
                    }, array_slice($marineResponse['daily']['time'], 0, 10), array_slice($marineResponse['daily']['wave_height_max'], 0, 10));

                    $dailyMarineData = array_map(function ($time, $wave_height_max, $wind_wave_height_max, $swell_wave_height_max, $wave_direction_dominant, $wind_wave_direction_dominant) {
                        return [
                            'date' => $time,
                            'wave_height_max' => $wave_height_max,
                            'wind_wave_height_max' => $wind_wave_height_max,
                            'swell_wave_height_max' => $swell_wave_height_max,
                            'wave_direction_dominant' => $wave_direction_dominant,
                            'wind_wave_direction_dominant' => $wind_wave_direction_dominant,
                        ];
                    }, array_slice($marineResponse['daily']['time'], 0, 10),
                       array_slice($marineResponse['daily']['wave_height_max'], 0, 10),
                       array_slice($marineResponse['daily']['wind_wave_height_max'], 0, 10),
                       array_slice($marineResponse['daily']['swell_wave_height_max'], 0, 10),
                       array_slice($marineResponse['daily']['wave_direction_dominant'], 0, 10),
                       array_slice($marineResponse['daily']['wind_wave_direction_dominant'], 0, 10));

                    $marineHourly = array_map(function ($time, $wave_height, $sea_surface_temperature, $sea_level_height_msl, $wave_direction, $wave_period, $wind_wave_height, $swell_wave_height) {
                        return [
                            'time' => $time,
                            'wave_height' => $wave_height,
                            'sea_surface_temperature' => $sea_surface_temperature,
                            'sea_level_height_msl' => $sea_level_height_msl,
                            'wave_direction' => $wave_direction,
                            'wave_period' => $wave_period,
                            'wind_wave_height' => $wind_wave_height,
                            'swell_wave_height' => $swell_wave_height,
                        ];
                    }, array_slice($marineResponse['hourly']['time'], 0, 240), // 10 days * 24 hours
                       array_slice($marineResponse['hourly']['wave_height'], 0, 240),
                       array_slice($marineResponse['hourly']['sea_surface_temperature'], 0, 240),
                       array_slice($marineResponse['hourly']['sea_level_height_msl'], 0, 240),
                       array_slice($marineResponse['hourly']['wave_direction'], 0, 240),
                       array_slice($marineResponse['hourly']['wave_period'], 0, 240),
                       array_slice($marineResponse['hourly']['wind_wave_height'], 0, 240),
                       array_slice($marineResponse['hourly']['swell_wave_height'], 0, 240));
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
            'daily_marine_data' => $dailyMarineData,
            'type' => $location->type,
            'altitude' => $location->altitude ?? 0,
            'marine_api_url' => $marineApiUrl
        ];

        $locations = ApiCache::getCached('locations') ?? Location::all()->toArray();
        if (!ApiCache::getCached('locations')) {
            ApiCache::setCached('locations', $locations, 24 * 60); // 24 hours
        }

        Log::info("Weather data sent to view for {$location->name}", ['weatherData' => $weatherData]);

        $view = 'weather.village-forecast';

        return view($view, [
            'location' => $location,
            'weatherData' => $weatherData,
            'locations' => collect($locations),
            'controller' => $this
        ]);
    }
}