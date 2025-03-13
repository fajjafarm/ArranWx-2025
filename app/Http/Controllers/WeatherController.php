<?php
namespace App\Http\Controllers;

use App\Services\WeatherService;
use App\Models\Location;
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



    public function warnings(Request $request)
    {
        // Simulated API response; replace with real data
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

        // Example dynamic logic (replace with API calls)
        /*
        if (rand(0, 1)) {
            $data['metOffice'] = ['class' => 'status-yellow', 'text' => 'Yellow Warning', 'updated' => Carbon::now('GMT')->format('d M Y H:i')];
            $data['sepaFlood'] = ['class' => 'status-amber', 'text' => '2 Alerts', 'updated' => Carbon::now('GMT')->format('d M Y H:i')];
        }
        */

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

       
    


        // Dynamic logic example (replace with real data source)
        /*
        $now = now();
        if ($now->isSameDay(Carbon::create(2025, 3, 10))) {
            $lochranzaClaonaigStatus = [
                'class' => 'status-amber',
                'text' => 'Disrupted (Wind)',
                'updated' => $now->format('d M Y H:i')
            ];
        }
        */
    
            // Example logic for dynamic status (uncomment and adapt if you have a data source)
            /*
            $today = now()->format('d M');
            if ($today === '12 Mar') {
                $brodickTroonStatus = [
                    'class' => 'status-amber',
                    'text' => 'Disrupted (Maintenance)'
                ];
            } elseif (someConditionForYellow()) {
                $brodickTroonStatus = [
                    'class' => 'status-yellow',
                    'text' => 'At Risk'
                ];
            } else {
                $brodickTroonStatus = [
                    'class' => 'status-green',
                    'text' => 'Normal Service'
                ];
            }
            */
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
        return view('dashboard', compact('weatherData', 'locations', 'brodickArdrossanStatus', 'brodickTroonStatus', 'lochranzaClaonaigStatus',
            'metOfficeWarning', 'meteogram', 'sepaFloodWarning'));
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
                $time = \Carbon\Carbon::parse($entry['time']);
                if ($time->minute === 0 && $time->hour % 2 === 0) {
                    $date = $time->toDateString();
                    $details = $entry['data']['instant']['details'];
                    $next1Hour = $entry['data']['next_1_hours'] , 'details' => ['precipitation_amount' => 0];
                    $next6Hour = $entry['data']['next_6_hours'] , 'details' => ['precipitation_amount' => 0]

                    // Calculate Gust
                    $windSpeed = $details['wind_speed'] ?? 0;
                    $cloudCover = $details['cloud_area_fraction'] ?? 0;
                    $pressure = $details['air_pressure_at_sea_level'] ?? null;
                    $altitude = $location->altitude ?? 0;

                    // Base gust factor: 1.5 for villages, 1.6 for hills (open topography)
                    $gustFactor = $location->type === 'Hill' ? 1.6 : 1.5;

                    // Adjust based on cloud cover
                    if ($cloudCover > 75) {
                        $gustFactor += 0.2; // Up to 1.8 (village) or 2.0 (hill)
                    } elseif ($cloudCover < 25) {
                        $gustFactor -= 0.1; // Down to 1.4 (village) or 1.5 (hill)
                    }

                    // Adjust based on pressure trend
                    if ($previousPressure !== null && $pressure !== null) {
                        $pressureChange = $previousPressure - $pressure;
                        if ($pressureChange > 1) {
                            $gustFactor += 0.2; // Stormy
                        } elseif ($pressureChange < -1) {
                            $gustFactor -= 0.1; // Stabilizing
                        }
                    }
                    $previousPressure = $pressure;

                    // Altitude multiplier for hills (1.5% increase per 100m)
                    $altitudeMultiplier = $location->type === 'Hill' ? (1 + ($altitude / 100) * 0.015) : 1;

                    // Use API gust if available, otherwise estimate
                    $windGust = $details['wind_speed_of_gust'] ?? ($windSpeed * $gustFactor * $altitudeMultiplier);

                    $hourlyData[$date][] = [
                        'time' => $time->format('H:i'),
                        'temperature' => $details['air_temperature'] ?? null,
                        'precipitation' => $next1Hour['details']['precipitation_amount'] ?? 0,
                        'condition' => $next1Hour['summary']['symbol_code'] ?? $next6Hour['summary']['symbol_code'],
                        'wind_speed' => $windSpeed,
                        'wind_gust' => round($windGust, 1),
                        'pressure' => $pressure,
                    ];
                }
            }
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
            'hourly' => $hourlyData,
            'sun_moon' => $sunMoonData,
            'marine' => $marine,
            'type' => $location->type,
            'altitude' => $location->altitude ?? 0,
        ];

        Log::info("Weather data sent to view for {$location->name}", $weatherData);

        return view('location', compact('location', 'weatherData'));
    }
}