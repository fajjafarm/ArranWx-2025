<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>10-Day Weather Forecast for {{ $location->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .weather-icon {
            width: 50px;
            height: 50px;
        }
        .day-card {
            background: linear-gradient(135deg, #e0f7fa, #b3e5fc);
            transition: transform 0.3s ease;
        }
        .day-card:hover {
            transform: scale(1.02);
        }
        .marine-info {
            background: linear-gradient(135deg, #bbdefb, #90caf9);
        }
        .text-shadow {
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-6 text-shadow">
            10-Day Weather Forecast for {{ $location->name }}
        </h1>

        @if (empty($weatherData['hourly']))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
                <p>No weather data available for {{ $location->name }}. Please try again later.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach (array_slice($weatherData['hourly'], 0, 10) as $date => $hourlyForecast)
                    @php
                        $day = \Carbon\Carbon::parse($date)->format('l, F j, Y');
                        $sunMoon = $weatherData['sun'][$date] ?? ['sunrise' => 'N/A', 'sunset' => 'N/A', 'moonrise' => 'N/A', 'moonset' => 'N/A'];
                        // Calculate daily summary (max/min temp, total precipitation, dominant condition)
                        $temps = array_column($hourlyForecast, 'temperature');
                        $maxTemp = !empty($temps) ? max($temps) : 'N/A';
                        $minTemp = !empty($temps) ? min($temps) : 'N/A';
                        $totalPrecip = array_sum(array_column($hourlyForecast, 'precipitation'));
                        $conditions = array_count_values(array_column($hourlyForecast, 'condition'));
                        $dominantCondition = array_key_first($conditions);
                        $maxGust = !empty($hourlyForecast) ? max(array_column($hourlyForecast, 'wind_gust')) : 'N/A';
                        $maxWindSpeed = !empty($hourlyForecast) ? max(array_column($hourlyForecast, 'wind_speed')) : 'N/A';
                        $dominantWindDirection = $controller->degreesToCardinal(
                            array_column($hourlyForecast, 'wind_from_direction_degrees')[array_key_first($hourlyForecast)] ?? null
                        );
                    @endphp

                    <div class="day-card rounded-lg shadow-lg p-4">
                        <h2 class="text-xl font-semibold text-gray-800 mb-2 text-shadow">{{ $day }}</h2>
                        <div class="flex items-center mb-2">
                            @if ($dominantCondition && file_exists(public_path("images/weather/{$dominantCondition}.png")))
                                <img src="{{ asset("images/weather/{$dominantCondition}.png") }}" alt="{{ $dominantCondition }}" class="weather-icon mr-2">
                            @else
                                <span class="text-gray-500">No Icon</span>
                            @endif
                            <div>
                                <p class="text-lg font-medium text-gray-700">{{ ucfirst(str_replace('_', ' ', $dominantCondition)) }}</p>
                                <p class="text-sm text-gray-600">High: {{ is_numeric($maxTemp) ? round($maxTemp, 1) . '°C' : 'N/A' }}</p>
                                <p class="text-sm text-gray-600">Low: {{ is_numeric($minTemp) ? round($minTemp, 1) . '°C' : 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="mb-2">
                            <p class="text-sm text-gray-600">Precipitation: {{ round($totalPrecip, 1) }} mm</p>
                            <p class="text-sm text-gray-600">Max Wind: {{ is_numeric($maxWindSpeed) ? round($maxWindSpeed, 1) . ' mph' : 'N/A' }} ({{ $dominantWindDirection }})</p>
                            <p class="text-sm text-gray-600">Max Gust: {{ is_numeric($maxGust) ? round($maxGust, 1) . ' mph' : 'N/A' }}</p>
                        </div>
                        <div class="mb-2">
                            <p class="text-sm text-gray-600">Sunrise: {{ $sunMoon['sunrise'] ? \Carbon\Carbon::parse($sunMoon['sunrise'])->format('H:i') : 'N/A' }}</p>
                            <p class="text-sm text-gray-600">Sunset: {{ $sunMoon['sunset'] ? \Carbon\Carbon::parse($sunMoon['sunset'])->format('H:i') : 'N/A' }}</p>
                            <p class="text-sm text-gray-600">Moonrise: {{ $sunMoon['moonrise'] ? \Carbon\Carbon::parse($sunMoon['moonrise'])->format('H:i') : 'N/A' }}</p>
                            <p class="text-sm text-gray-600">Moonset: {{ $sunMoon['moonset'] ? \Carbon\Carbon::parse($sunMoon['moonset'])->format('H:i') : 'N/A' }}</p>
                        </div>
                        @if ($location->type === 'Marine' && isset($weatherData['daily_marine_data']))
                            @php
                                $marineDay = collect($weatherData['daily_marine_data'])->firstWhere('date', $date);
                            @endphp
                            @if ($marineDay)
                                <div class="marine-info rounded p-2 mt-2">
                                    <p class="text-sm text-gray-600">Max Wave Height: {{ $marineDay['wave_height_max'] ?? 'N/A' }} m</p>
                                    <p class="text-sm text-gray-600">Max Wind Wave Height: {{ $marineDay['wind_wave_height_max'] ?? 'N/A' }} m</p>
                                    <p class="text-sm text-gray-600">Max Swell Wave Height: {{ $marineDay['swell_wave_height_max'] ?? 'N/A' }} m</p>
                                    <p class="text-sm text-gray-600">Wave Direction: {{ $controller->degreesToCardinal($marineDay['wave_direction_dominant'] ?? null) }}</p>
                                </div>
                            @endif
                        @endif
                        <div class="mt-2">
                            <h3 class="text-sm font-semibold text-gray-700">Hourly Breakdown</h3>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach ($hourlyForecast as $hour)
                                    <div class="bg-white rounded p-2 shadow">
                                        <p class="text-xs font-medium">{{ $hour['time'] }}</p>
                                        <p class="text-xs">Temp: {{ is_numeric($hour['temperature']) ? round($hour['temperature'], 1) . '°C' : 'N/A' }}</p>
                                        <p class="text-xs">Precip: {{ round($hour['precipitation'], 1) }} mm</p>
                                        <p class="text-xs">Wind: {{ round($hour['wind_speed'], 1) }} mph ({{ $hour['wind_direction'] }})</p>
                                        <p class="text-xs">Gust: {{ round($hour['wind_gust'], 1) }} mph</p>
                                        @if ($hour['condition'] && file_exists(public_path("images/weather/{$hour['condition']}.png")))
                                            <img src="{{ asset("images/weather/{$hour['condition']}.png") }}" alt="{{ $hour['condition'] }}" class="weather-icon mx-auto">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</body>
</html>