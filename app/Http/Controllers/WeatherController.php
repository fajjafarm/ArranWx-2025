<?php

namespace App\Http\Controllers;

use App\Services\WeatherService;
use App\Models\Location;
use App\Models\ApiCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
                'updated' => Carbon::now('Europe/London')->format('Y-m-d H:i'),
            ],
            'meteogram' => [
                'imageUrl' => 'https://api.met.no/weatherapi/nowcast/2.0/meteogram?lat=55.6&lon=-5.3', // Example for Arran
                'updated' => Carbon::now('Europe/London')->format('Y-m-d H:i'),
            ],
            'sepaFlood' => [
                'class' => 'weather-no-warning',
                'text' => 'No Warnings',
                'updated' => Carbon::now('Europe/London')->format('Y-m-d H:i'),
            ],
        ];

        return response()->json($data);
    }

    public function index()
    {
        $brodickArdrossanStatus = [
            'class' => 'status-red',
            'text' => 'No Service',
            'updated' => Carbon::create(2025, 1, 13, 5, 37, 0, 'Europe/London')->format('d M Y H:i'),
        ];

        $brodickTroonStatus = [
            'class' => 'status-amber',
            'text' => 'Disrupted',
            'updated' => Carbon::create(2025, 3, 12, 12, 0, 0, 'Europe/London')->format('d M Y H:i'),
        ];

        $lochranzaClaonaigStatus = [
            'class' => 'status-green',
            'text' => 'Normal',
            'updated' => Carbon::create(2025, 3, 10, 11, 47, 0, 'Europe/London')->format('d M Y H:i'),
        ];

        $metOfficeWarning = [
            'class' => 'weather-no-warning',
            'text' => 'No Warnings',
            'updated' => Carbon::now('Europe/London')->format('Y-m-d H:i'),
        ];

        $meteogram = [
            'imageUrl' => 'https://api.met.no/weatherapi/nowcast/2.0/meteogram?lat=55.6&lon=-5.3',
            'updated' => Carbon::now('Europe/London')->format('Y-m-d H:i'),
        ];

        $sepaFloodWarning = [
            'class' => 'weather-no-warning',
            'text' => 'No Warnings',
            'updated' => Carbon::now('Europe/London')->format('Y-m-d H:i'),
        ];

        // Cache locations for 24 hours
        $locations = ApiCache::getCached('locations') ?? Location::all()->toArray();
        if (!ApiCache::getCached('locations')) {
            ApiCache::setCached('locations', $locations, 24 * 60); // 24 hours
        }

        $weatherData = [];

        foreach ($locations as $location) {
            $location = (object) $location;
            Log::info("Fetching weather data for {$location->name}");

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

            $next1Hour = isset($weather['properties']['timeseries'][0]['data']['next_1_hours'])
                ? $weather['properties']['timeseries'][0]['data']['next_1_hours']['details']
                : ['precipitation_amount' => 0];

            $marine = null;
            if ($location->type === 'Marine') {
                $marineCacheKey = "marine_{$location->latitude}_{$location->longitude}";
                $marineResponse = ApiCache::getCached($marineCacheKey);
                if (!$marineResponse) {
                    $marineResponse = $this->weatherService->getMarineForecast($location->latitude, $location->longitude);
                    if (!empty($marineResponse)) {
                        ApiCache::setCached($marineCacheKey, $marineResponse, 60); // 1 hour
                    }
                }
                if ($marineResponse && isset($marineResponse['properties']['timeseries'][0]['data']['instant']['details'])) {
                    $marineDetails = $marineResponse['properties']['timeseries'][0]['data']['instant']['details'];
                    $marine = [
                        'wave_height' => $marineDetails['sea_water_wave_height'] ?? null,
                        'wave_direction' => $marineDetails['sea_water_wave_from_direction'] ?? null,
                        'sea_surface_temperature' => $marineDetails['sea_surface_temperature'] ?? null,
                    ];
                }
            }

            $weatherData[$location->name] = [
                'weather' => $weatherDetails,
                'precipitation' => $next1Hour['precipitation_amount'] ?? 0,
                'wind_direction' => $this->degreesToCardinal($weatherDetails['wind_from_direction'] ?? null),
                'wind_from_direction_degrees' => $weatherDetails['wind_from_direction'] ?? null,
                'marine' => $marine,
                'type' => $location->type,
                'altitude' => $location->altitude ?? 0,
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
            ];
        }

        Log::info("Weather data sent to dashboard view", ['weather_data' => $weatherData]);
        return view('weather.dashboard', compact('weatherData', 'locations', 'brodickArdrossanStatus', 'brodickTroonStatus', 'lochranzaClaonaigStatus', 'metOfficeWarning', 'meteogram', 'sepaFloodWarning'));
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
                'route_uri' => $routeUri,
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
                'snowandthunder' => 'snowandthunder',
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
                    ApiCache::setCached($sunMoonCacheKey, $sunMoon, 24 * 60); // 24 hours
                }
            }
            $moonPhase = isset($sunMoon['moonphase']) && is_numeric($sunMoon['moonphase']) ? $sunMoon['moonphase'] / 360 : null;
            $sunMoonData[$date] = [
                'sunrise' => $sunMoon['sunrise'] ?? null,
                'sunset' => $sunMoon['sunset'] ?? null,
                'moonrise' => $sunMoon['moonrise'] ?? null,
                'moonset' => $sunMoon['moonset'] ?? null,
                'moonphase' => $moonPhase,
            ];
            if (in_array(null, [$sunMoonData[$date]['sunrise'], $sunMoonData[$date]['sunset'], $sunMoonData[$date]['moonrise'], $sunMoonData[$date]['moonset']], true)) {
                Log::warning("Missing or invalid sun/moon data for {$location->name} on {$date}", ['sun_moon' => $sunMoon]);
            }
        }
        Log::info("Sun and moon data for {$location->name}", ['sun_moon' => $sunMoonData]);

        $marine = null;
        $marineForecast = [];
        $marineHourly = [];
        $dailyMarineData = [];
        $marineApiUrl = null;
        if ($location->type === 'Marine') {
            Log::info("Fetching marine data for {$location->name}", ['lat' => $location->latitude, 'lon' => $location->longitude]);
            try {
                $currentDate = Carbon::now('Europe/London');
                $isBst = $currentDate->isDST();
                $apiTimezone = $isBst ? 'Europe/London' : 'GMT';

                $marineApiUrl = $this->baseUrl . 'oceanforecast/2.0/complete?' . http_build_query([
                    'lat' => $location->latitude,
                    'lon' => $location->longitude,
                ]);

                $marineCacheKey = "marine_{$location->latitude}_{$location->longitude}";
                $marineResponse = ApiCache::getCached($marineCacheKey);
                if (!$marineResponse) {
                    $marineResponse = $this->weatherService->getMarineForecast($location->latitude, $location->longitude);
                    if (!empty($marineResponse)) {
                        ApiCache::setCached($marineCacheKey, $marineResponse, 60); // 1 hour
                    }
                }

                Log::info("Raw marine data for {$location->name}", ['url' => $marineApiUrl, 'data' => $marineResponse]);

                if ($marineResponse && isset($marineResponse['properties']['timeseries'])) {
                    $marineTimeseries = $marineResponse['properties']['timeseries'];
                    $marine = [
                        'wave_height' => $marineTimeseries[0]['data']['instant']['details']['sea_water_wave_height'] ?? null,
                        'wave_direction' => $marineTimeseries[0]['data']['instant']['details']['sea_water_wave_from_direction'] ?? null,
                        'wave_period' => $marineTimeseries[0]['data']['instant']['details']['sea_water_wave_period'] ?? null,
                        'wind_wave_height' => $marineTimeseries[0]['data']['instant']['details']['wind_wave_height'] ?? null,
                        'swell_wave_height' => $marineTimeseries[0]['data']['instant']['details']['swell_wave_height'] ?? null,
                        'swell_wave_direction' => $marineTimeseries[0]['data']['instant']['details']['swell_wave_from_direction'] ?? null,
                        'swell_wave_period' => $marineTimeseries[0]['data']['instant']['details']['swell_wave_period'] ?? null,
                        'sea_surface_temperature' => $marineTimeseries[0]['data']['instant']['details']['sea_surface_temperature'] ?? null,
                        'sea_level_height_msl' => $marineTimeseries[0]['data']['instant']['details']['sea_level_height'] ?? null,
                    ];

                    // Process hourly marine data (2-hourly for 10 days)
                    foreach ($marineTimeseries as $entry) {
                        $time = Carbon::parse($entry['time'])->setTimezone('Europe/London');
                        if ($time->minute === 0 && $time->hour % 2 === 0 && $time->diffInDays(Carbon::now('Europe/London')) <= 10) {
                            $date = $time->toDateString();
                            $details = $entry['data']['instant']['details'];
                            $marineHourly[$date][] = [
                                'time' => $time->format('H:i'),
                                'wave_height' => $details['sea_water_wave_height'] ?? null,
                                'sea_surface_temperature' => $details['sea_surface_temperature'] ?? null,
                                'sea_level_height_msl' => $details['sea_level_height'] ?? null,
                                'wave_direction' => $details['sea_water_wave_from_direction'] ?? null,
                                'wave_period' => $details['sea_water_wave_period'] ?? null,
                                'wind_wave_height' => $details['wind_wave_height'] ?? null,
                                'swell_wave_height' => $details['swell_wave_height'] ?? null,
                            ];
                        }
                    }
                    $marineHourly = array_slice($marineHourly, 0, 10, true);

                    // Daily marine data (aggregate max values)
                    $dailyMarineData = [];
                    foreach (array_keys($marineHourly) as $date) {
                        $dayData = $marineHourly[$date];
                        $maxWaveHeight = max(array_column($dayData, 'wave_height') ?: [0]);
                        $maxWindWaveHeight = max(array_column($dayData, 'wind_wave_height') ?: [0]);
                        $maxSwellWaveHeight = max(array_column($dayData, 'swell_wave_height') ?: [0]);
                        $dominantWaveDirection = $dayData[0]['wave_direction'] ?? null;
                        $dominantWindWaveDirection = $dayData[0]['wind_wave_height'] ? ($dayData[0]['wave_direction'] ?? null) : null;
                        $dailyMarineData[$date] = [
                            'date' => $date,
                            'wave_height_max' => $maxWaveHeight,
                            'wind_wave_height_max' => $maxWindWaveHeight,
                            'swell_wave_height_max' => $maxSwellWaveHeight,
                            'wave_direction_dominant' => $dominantWaveDirection,
                            'wind_wave_direction_dominant' => $dominantWindWaveDirection,
                        ];
                    }

                    $marineForecast = array_map(function ($data) {
                        return [
                            'date' => $data['date'],
                            'wave_height_max' => $data['wave_height_max'],
                        ];
                    }, array_values($dailyMarineData));
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
            'marine_api_url' => $marineApiUrl,
        ];

        $locations = ApiCache::getCached('locations') ?? Location::all()->toArray();
        if (!ApiCache::getCached('locations')) {
            ApiCache::setCached('locations', $locations, 24 * 60); // 24 hours
        }

        Log::info("Weather data sent to view for {$location->name}", ['weatherData' => $weatherData]);

        $view = $location->type === 'Marine' ? 'weather.marine-forecast' : 'weather.village-forecast';

        return view($view, [
            'location' => $location,
            'weatherData' => $weatherData,
            'locations' => collect($locations),
            'controller' => $this,
        ]);
    }
    public function tide(Request $request, $name)
{
    $location = Location::where('name', $name)->firstOrFail();

    if ($location->type !== 'Marine') {
        Log::warning("Tide forecast requested for non-marine location: {$location->name}");
        abort(404, 'Tide forecasts are only available for marine locations.');
    }

    Log::info("Fetching tide data for {$location->name}", ['lat' => $location->latitude, 'lon' => $location->longitude]);

    $marineCacheKey = "marine_{$location->latitude}_{$location->longitude}";
    $marineResponse = ApiCache::getCached($marineCacheKey);
    if (!$marineResponse) {
        $marineResponse = $this->weatherService->getMarineForecast($location->latitude, $location->longitude);
        if (!empty($marineResponse)) {
            ApiCache::setCached($marineCacheKey, $marineResponse, 60); // 1 hour
        }
    }

    $tideData = ['hourly' => []];
    if ($marineResponse && isset($marineResponse['properties']['timeseries'])) {
        $timeseries = $marineResponse['properties']['timeseries'];
        foreach ($timeseries as $entry) {
            $time = Carbon::parse($entry['time'])->setTimezone('Europe/London');
            if ($time->minute === 0 && $time->hour % 2 === 0 && $time->diffInDays(Carbon::now('Europe/London')) <= 7) {
                $date = $time->toDateString();
                $details = $entry['data']['instant']['details'];
                $tideData['hourly'][$date][] = [
                    'time' => $time->format('H:i'),
                    'sea_level_height_msl' => $details['sea_level_height'] ?? null,
                ];
            }
        }
        $tideData['hourly'] = array_slice($tideData['hourly'], 0, 7, true); // 7 days
        Log::info("Processed tide data for {$location->name}", ['days' => count($tideData['hourly'])]);
    } else {
        Log::error("Invalid marine response for {$location->name}", ['response' => $marineResponse]);
    }

    $locations = ApiCache::getCached('locations') ?? Location::all()->toArray();
    if (!ApiCache::getCached('locations')) {
        ApiCache::setCached('locations', $locations, 24 * 60); // 24 hours
    }

    return view('weather.tide-forecast', [
        'location' => $location,
        'tideData' => $tideData,
        'locations' => collect($locations),
        'controller' => $this,
    ]);
}
}