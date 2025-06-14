@extends('layouts.vertical', ['title' => $location->name . ' Weather'])

@section('html-attribute')
    data-sidenav-size="full"
@endsection

@section('css')
    @vite(['node_modules/flatpickr/dist/flatpickr.min.css'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/weather-icons/2.0.10/css/weather-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.min.css" crossorigin="">
    <style>
        .forecast-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .forecast-table th,
        .forecast-table td {
            padding: 8px;
            text-align: center;
            border-bottom: 1px solid #ddd;
            font-size: 14px;
            vertical-align: middle;
        }
        .forecast-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .day-header {
            background-color: #e9ecef;
            font-size: 16px;
            padding: 10px;
            text-align: left;
        }
        .weather-icon {
            font-size: 24px;
            color: #555;
        }
        .sun-moon-icon {
            font-size: 18px;
            color: #777;
            margin-right: 5px;
            vertical-align: middle;
        }
        .day-header span {
            margin-right: 15px;
        }
        .wind-direction {
            width: 36px;
            height: 36px;
            position: relative;
            margin: 0 auto;
            display: inline-block;
            vertical-align: middle;
        }
        .wind-arrow {
            width: 0;
            height: 0;
            border-left: 8px solid transparent;
            border-right: 8px solid transparent;
            border-bottom: 16px solid #000000;
            position: absolute;
            top: 50%;
            left: 50%;
            transform-origin: center bottom;
            z-index: 2;
        }
        .wind-arrow-tail {
            position: absolute;
            width: 6px;
            background: #000000;
            top: -10px;
            left: -3px;
            z-index: 1;
        }
        .wind-dir-text {
            font-size: 12px;
            font-weight: bold;
        }
        .beaufort-key,
        .gradient-key {
            margin-top: 20px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .beaufort-key table,
        .gradient-key table {
            width: 100%;
            border-collapse: collapse;
        }
        .beaufort-key td,
        .gradient-key td {
            padding: 5px;
            text-align: center;
            font-size: 12px;
        }
        #leaflet-map {
            height: 200px;
            width: 100%;
            border-radius: 5px;
            margin-bottom: 10px;
            z-index: 0;
        }
        @media (max-width: 768px) {
            .forecast-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
            .forecast-table th,
            .forecast-table td {
                padding: 6px;
                font-size: 12px;
            }
            .weather-icon {
                font-size: 18px;
            }
            .sun-moon-icon {
                font-size: 14px;
                margin-right: 3px;
            }
            .day-header {
                font-size: 14px;
            }
            .day-header span {
                margin-right: 10px;
            }
            .beaufort-key td,
            .gradient-key td {
                font-size: 10px;
                padding: 3px;
            }
            .wind-direction {
                width: 24px;
                height: 24px;
            }
            .wind-arrow {
                border-left: 5px solid transparent;
                border-right: 5px solid transparent;
                border-bottom: 10px solid #000000;
            }
            .wind-arrow-tail {
                width: 4px;
                top: -6px;
                left: -2px;
            }
            .wind-dir-text {
                font-size: 10px;
            }
            #leaflet-map {
                height: 150px;
            }
        }
    </style>
@endsection

@section('content')
    @include('layouts.partials.page-title', ['subtitle' => 'Weather Details', 'title' => $location->name])

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <!-- Header: Leaflet Map, Title, Description -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div id="leaflet-map"></div>
                        </div>
                        <div class="col-md-9 text-start">
                            <h2 class="card-title">{{ $location->name }} Weather Forecast</h2>
                            <p class="text-muted">
                                {{ $location->description ?? 'Weather forecast for ' . $location->name . ', located at latitude ' . $location->latitude . ', longitude ' . $location->longitude . ', altitude ' . ($location->altitude ?? 0) . ' meters above sea level.' }}
                            </p>
                        </div>
                    </div>

                    <!-- Current Conditions -->
                    <div class="text-center">
                        <h5 class="text-muted fs-13 text-uppercase">
                            {{ $location->name }} ({{ $location->altitude ?? 0 }}m) - Current Conditions
                        </h5>
                        <div class="d-flex align-items-center justify-content-center gap-2 my-2 py-1">
                            <div class="user-img fs-42 flex-shrink-0">
                                <span class="avatar-title {{ $weatherData['type'] === 'Hill' ? 'text-bg-success' : 'text-bg-primary' }} rounded-circle fs-22">
                                    <iconify-icon icon="{{ $weatherData['type'] === 'Hill' ? 'solar:mountains-bold-duotone' : 'solar:buildings-bold-duotone' }}"></iconify-icon>
                                </span>
                            </div>
                            <h3 class="mb-0 fw-bold">
                                @if (isset($weatherData['current']['air_temperature']))
                                    {{ number_format($weatherData['current']['air_temperature'], 1) }}°C
                                @else
                                    N/A
                                @endif
                            </h3>
                        </div>
                        @if (isset($weatherData['current']))
                            <p class="mb-1 text-muted">
                                <span class="me-2">Wind: {{ $weatherData['current']['wind_speed'] ?? 'N/A' }} m/s</span>
                                <span>Humidity: {{ $weatherData['current']['relative_humidity'] ?? 'N/A' }}%</span>
                            </p>
                        @endif
                    </div>

                    <!-- Charts -->
                    @if (!empty($weatherData['hourly']))
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <h6 class="text-muted fs-14 text-center">7-Day Temperature Forecast</h6>
                                <canvas id="temperatureChart" style="max-height: 200px; margin: 20px auto;"></canvas>
                            </div>
                            <div class="col-md-4">
                                <h6 class="text-muted fs-14 text-center">7-Day Rainfall Total</h6>
                                <canvas id="rainfallChart" style="max-height: 200px; margin: 20px auto;"></canvas>
                            </div>
                            <div class="col-md-4">
                                <h6 class="text-muted fs-14 text-center">7-Day Peak Wind Gust</h6>
                                <canvas id="windGustChart" style="max-height: 200px; margin: 20px auto;"></canvas>
                            </div>
                        </div>
                    @endif

                    <!-- Hourly Weather Forecast -->
                    @if (in_array($weatherData['type'], ['Village', 'Hill']) && !empty($weatherData['hourly']))
                        <h6 class="text-muted fs-14 mt-4">Hourly Weather Forecast (Every 2 Hours)</h6>
                        @php
                            $beaufortRanges = [
                                0 => ['max' => 0.5, 'desc' => 'Calm', 'color' => '#e6ffe6'],
                                1 => ['max' => 1.5, 'desc' => 'Light Air', 'color' => '#ccffcc'],
                                2 => ['max' => 3.3, 'desc' => 'Light Breeze', 'color' => '#b3ffb3'],
                                3 => ['max' => 5.5, 'desc' => 'Gentle Breeze', 'color' => '#99ff99'],
                                4 => ['max' => 7.9, 'desc' => 'Moderate Breeze', 'color' => '#80ff80'],
                                5 => ['max' => 10.7, 'desc' => 'Fresh Breeze', 'color' => '#ffff99'],
                                6 => ['max' => 13.8, 'desc' => 'Strong Breeze', 'color' => '#ffeb3b'],
                                7 => ['max' => 17.1, 'desc' => 'Near Gale', 'color' => '#ffcc80'],
                                8 => ['max' => 20.7, 'desc' => 'Gale', 'color' => '#ff9800'],
                                9 => ['max' => 24.4, 'desc' => 'Strong Gale', 'color' => '#ff6666'],
                                10 => ['max' => 28.4, 'desc' => 'Storm', 'color' => '#ff3333'],
                                11 => ['max' => 32.6, 'desc' => 'Violent Storm', 'color' => '#cc0000'],
                                12 => ['max' => PHP_FLOAT_MAX, 'desc' => 'Hurricane Force', 'color' => '#800000']
                            ];

                            $tempRanges = [
                                ['min' => -10, 'max' => -7, 'color' => '#00b7eb'],
                                ['min' => -6.9, 'max' => -4, 'color' => '#33c4f0'],
                                ['min' => -3.9, 'max' => -1, 'color' => '#66d1f5'],
                                ['min' => -0.9, 'max' => 2, 'color' => '#99ebff'],
                                ['min' => 2.1, 'max' => 5, 'color' => '#b3f0ff'],
                                ['min' => 5.1, 'max' => 8, 'color' => '#ccffcc'],
                                ['min' => 8.1, 'max' => 11, 'color' => '#e6ffe6'],
                                ['min' => 11.1, 'max' => 14, 'color' => '#ffffcc'],
                                ['min' => 14.1, 'max' => 17, 'color' => '#fff099'],
                                ['min' => 17.1, 'max' => 20, 'color' => '#ffeb3b'],
                                ['min' => 20.1, 'max' => 23, 'color' => '#ffd700'],
                                ['min' => 23.1, 'max' => 26, 'color' => '#ffcc80'],
                                ['min' => 26.1, 'max' => 29, 'color' => '#ff9966'],
                                ['min' => 29.1, 'max' => 32, 'color' => '#ff6666'],
                                ['min' => 32.1, 'max' => PHP_FLOAT_MAX, 'color' => '#ff3333']
                            ];

                            function formatTime($value) {
                                if ($value && $value !== 'N/A') {
                                    try {
                                        return \Carbon\Carbon::parse($value)->format('H:i');
                                    } catch (\Exception $e) {
                                        return 'N/A';
                                    }
                                }
                                return 'N/A';
                            }
                        @endphp
                        @foreach ($weatherData['hourly'] as $date => $hours)
                            <div class="day-header">
                                {{ \Carbon\Carbon::parse($date)->format('l, M d') }}
                                <span><i class="wi wi-sunrise sun-moon-icon"></i> {{ formatTime($weatherData['sun'][$date]['sunrise'] ?? 'N/A') }}</span>
                                <span><i class="wi wi-sunset sun-moon-icon"></i> {{ formatTime($weatherData['sun'][$date]['sunset'] ?? 'N/A') }}</span>
                                <span><i class="wi wi-moonrise sun-moon-icon"></i> {{ formatTime($weatherData['sun'][$date]['moonrise'] ?? 'N/A') }}</span>
                                <span><i class="wi wi-moonset sun-moon-icon"></i> {{ formatTime($weatherData['sun'][$date]['moonset'] ?? 'N/A') }}</span>
                                <span>
                                    @php
                                        $moonPhase = $weatherData['sun'][$date]['moonphase'] ?? null;
                                        $moonIcon = 'wi-na';
                                        if (is_numeric($moonPhase)) {
                                            switch (true) {
                                                case $moonPhase < 0.05:
                                                    $moonIcon = 'wi-moon-new';
                                                    break;
                                                case $moonPhase < 0.15:
                                                    $moonIcon = 'wi-moon-waxing-crescent-3';
                                                    break;
                                                case $moonPhase < 0.25:
                                                    $moonIcon = 'wi-moon-first-quarter';
                                                    break;
                                                case $moonPhase < 0.35:
                                                    $moonIcon = 'wi-moon-waxing-gibbous-3';
                                                    break;
                                                case $moonPhase < 0.45:
                                                    $moonIcon = 'wi-moon-waxing-gibbous-6';
                                                    break;
                                                case $moonPhase < 0.55:
                                                    $moonIcon = 'wi-moon-full';
                                                    break;
                                                case $moonPhase < 0.65:
                                                    $moonIcon = 'wi-moon-waning-gibbous-3';
                                                    break;
                                                case $moonPhase < 0.75:
                                                    $moonIcon = 'wi-moon-third-quarter';
                                                    break;
                                                case $moonPhase < 0.85:
                                                    $moonIcon = 'wi-moon-waning-crescent-3';
                                                    break;
                                                default:
                                                    $moonIcon = 'wi-moon-waning-crescent-6';
                                                    break;
                                            }
                                        }
                                    @endphp
                                    <i class="wi {{ $moonIcon }} sun-moon-icon"></i> {{ $moonPhase !== null ? number_format($moonPhase, 2) : 'N/A' }}
                                </span>
                            </div>
                            <table class="forecast-table">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Condition</th>
                                        <th>Temp (°C)</th>
                                        <th>Wind (m/s)</th>
                                        <th>Gusts (m/s)</th>
                                        <th>Arrow</th>
                                        <th>Dir</th>
                                        <th>Rain (mm)</th>
                                        <th>Pressure (hPa)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (collect($hours)->filter(function ($hour, $index) { return $index % 2 === 0; }) as $hour)
                                        @php
                                            $condition = $hour['condition'] ?? 'unknown';
                                            $iconMap = [
                                                'clearsky_day' => 'wi-day-sunny',
                                                'clearsky_night' => 'wi-night-clear',
                                                'fair_day' => 'wi-day-sunny-overcast',
                                                'fair_night' => 'wi-night-partly-cloudy',
                                                'partlycloudy_day' => 'wi-day-cloudy',
                                                'partlycloudy_night' => 'wi-night-alt-cloudy',
                                                'cloudy' => 'wi-cloudy',
                                                'rain' => 'wi-rain',
                                                'rainshowers_day' => 'wi-day-showers',
                                                'rainshowers_night' => 'wi-night-alt-showers',
                                                'lightrain' => 'wi-sprinkle',
                                                'lightrainshowers_day' => 'wi-day-sprinkle',
                                                'lightrainshowers_night' => 'wi-night-alt-sprinkle',
                                                'heavyrain' => 'wi-rain-wind',
                                                'heavyrainshowers_day' => 'wi-day-rain',
                                                'heavyrainshowers_night' => 'wi-night-alt-rain',
                                                'snow' => 'wi-snow',
                                                'snowshowers_day' => 'wi-day-snow',
                                                'snowshowers_night' => 'wi-night-alt-snow',
                                                'lightsnow' => 'wi-snowflake-cold',
                                                'lightsnowshowers_day' => 'wi-day-snow-wind',
                                                'lightsnowshowers_night' => 'wi-night-alt-snow-wind',
                                                'sleet' => 'wi-sleet',
                                                'sleetsnowers_day' => 'wi-day-sleet',
                                                'sleetsnowers_night' => 'wi-night-alt-sleet',
                                                'thunder' => 'wi-thunderstorm',
                                                'rainandthunder' => 'wi-storm-showers',
                                                'snowandthunder' => 'wi-snow-thunderstorm',
                                                'fog' => 'wi-fog'
                                            ];
                                            $iconClass = $iconMap[$condition] ?? 'wi-na';

                                            $windSpeed = $hour['wind_speed'] ?? 0;
                                            $windBeaufortLevel = 0;
                                            $windBeaufortDesc = 'N/A';
                                            $windColor = 'background: #e6ffe6;';
                                            if (is_numeric($windSpeed)) {
                                                foreach ($beaufortRanges as $level => $range) {
                                                    if ($windSpeed <= $range['max']) {
                                                        $windBeaufortLevel = $level;
                                                        $windBeaufortDesc = $range['desc'];
                                                        $windColor = "background: {$range['color']};";
                                                        break;
                                                    }
                                                }
                                            }

                                            $windGust = $hour['wind_gust'] ?? 0;
                                            $gustBeaufortLevel = 0;
                                            $gustBeaufortDesc = 'N/A';
                                            $gustColor = 'background: #e6ffe6;';
                                            if (is_numeric($windGust)) {
                                                foreach ($beaufortRanges as $level => $range) {
                                                    if ($windGust <= $range['max']) {
                                                        $gustBeaufortLevel = $level;
                                                        $gustBeaufortDesc = $range['desc'];
                                                        $gustColor = "background: {$range['color']};";
                                                        break;
                                                    }
                                                }
                                            }

                                            $temp = $hour['temperature'] ?? null;
                                            $tempColor = 'background: #e6ffe6;';
                                            if (is_numeric($temp)) {
                                                foreach ($tempRanges as $range) {
                                                    if ($temp >= $range['min'] && $temp <= $range['max']) {
                                                        $tempColor = "background: {$range['color']};";
                                                        break;
                                                    }
                                                }
                                            }

                                            $rain = $hour['precipitation'] ?? 0;
                                            $rainColor = 'background: transparent;';
                                            if (is_numeric($rain)) {
                                                if ($rain == 0) $rainColor = 'background: transparent;';
                                                elseif ($rain <= 2) $rainColor = 'background: #e6f3ff;';
                                                elseif ($rain <= 5) $rainColor = 'background: #99ccff;';
                                                elseif ($rain <= 10) $rainColor = 'background: #3399ff;';
                                                else $rainColor = 'background: #0066cc;';
                                            }

                                            $pressure = $hour['pressure'] ?? null;
                                            $pressureColor = 'background: #e6ffe6;';
                                            if (is_numeric($pressure)) {
                                                if ($pressure < 970) $pressureColor = 'background: #ff6666;';
                                                elseif ($pressure <= 990) $pressureColor = 'background: #ffcc80;';
                                                elseif ($pressure <= 1010) $pressureColor = 'background: #ccffcc;';
                                                elseif ($pressure <= 1030) $pressureColor = 'background: #99ebff;';
                                                else $pressureColor = 'background: #66d1f5;';
                                            }

                                            $tailLength = is_numeric($windSpeed) ? min($windSpeed * 2, 20) : 0;
                                            $mobileTailLength = is_numeric($windSpeed) ? min($windSpeed * 1.5, 12) : 0;
                                        @endphp
                                        <tr>
                                            <td>{{ $hour['time'] }}</td>
                                            <td><i class="wi {{ $iconClass }} weather-icon" title="{{ $condition }}"></i></td>
                                            <td style="{{ $tempColor }}">{{ $hour['temperature'] ?? 'N/A' }}</td>
                                            <td style="{{ $windColor }}" title="{{ $windBeaufortLevel }}: {{ $windBeaufortDesc }}">{{ $hour['wind_speed'] ?? 'N/A' }}</td>
                                            <td style="{{ $gustColor }}" title="{{ $gustBeaufortLevel }}: {{ $gustBeaufortDesc }}">{{ $hour['wind_gust'] ?? 'N/A' }}{{ !isset($hour['wind_gust']) && isset($hour['wind_speed']) ? '*' : '' }}</td>
                                            <td>
                                                @if (isset($hour['wind_from_direction_degrees']))
                                                    <div class="wind-direction" title="{{ $hour['wind_direction'] }} ({{ $hour['wind_from_direction_degrees'] }}°)">
                                                        <div class="wind-arrow" style="transform: translate(-50%, -50%) rotate({{ $hour['wind_from_direction_degrees'] - 180 }}deg);">
                                                            <div class="wind-arrow-tail" style="height: {{ $tailLength }}px; @media (max-width: 768px) { height: {{ $mobileTailLength }}px; }"></div>
                                                        </div>
                                                    </div>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td class="wind-dir-text">{{ $hour['wind_direction'] ?? 'N/A' }}</td>
                                            <td style="{{ $rainColor }}">{{ $hour['precipitation'] ?? 'N/A' }}</td>
                                            <td style="{{ $pressureColor }}">{{ $hour['pressure'] ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endforeach
                        <p class="text-muted fs-12 mt-2">* Gusts estimated based on wind speed, weather conditions, and {{ $weatherData['type'] === 'Hill' ? 'altitude' : 'rural factors' }}.</p>

                        <!-- Beaufort Scale Key -->
                        <div class="beaufort-key">
                            <h6 class="text-muted fs-14">Beaufort Scale Key (Wind & Gusts)</h6>
                            <table>
                                <tr>
                                    <td style="background: #e6ffe6;">0: Calm (< 0.5 m/s)</td>
                                    <td style="background: #ccffcc;">1: Light Air (0.5–1.5 m/s)</td>
                                    <td style="background: #b3ffb3;">2: Light Breeze (1.6–3.3 m/s)</td>
                                    <td style="background: #99ff99;">3: Gentle Breeze (3.4–5.5 m/s)</td>
                                </tr>
                                <tr>
                                    <td style="background: #80ff80;">4: Moderate Breeze (5.6–7.9 m/s)</td>
                                    <td style="background: #ffff99;">5: Fresh Breeze (8.0–10.7 m/s)</td>
                                    <td style="background: #ffeb3b;">6: Strong Breeze (10.8–13.8 m/s)</td>
                                    <td style="background: #ffcc80;">7: Near Gale (13.9–17.1 m/s)</td>
                                </tr>
                                <tr>
                                    <td style="background: #ff9800;">8: Gale (17.2–20.7 m/s)</td>
                                    <td style="background: #ff6666;">9: Strong Gale (20.8–24.4 m/s)</td>
                                    <td style="background: #ff3333;">10: Storm (24.5–28.4 m/s)</td>
                                    <td style="background: #cc0000;">11: Violent Storm (28.5–32.6 m/s)</td>
                                </tr>
                                <tr>
                                    <td colspan="4" style="background: #800000;">12: Hurricane Force (> 32.6 m/s)</td>
                                </tr>
                            </table>
                        </div>

                        <!-- Additional Color Keys -->
                        <div class="gradient-key">
                            <h6 class="text-muted fs-14">Temperature Key (°C)</h6>
                            <table>
                                <tr>
                                    <td style="background: #00b7eb;">-10 to -7</td>
                                    <td style="background: #33c4f0;">-6.9 to -4</td>
                                    <td style="background: #66d1f5;">-3.9 to -1</td>
                                    <td style="background: #99ebff;">-0.9 to 2</td>
                                    <td style="background: #b3f0ff;">2.1 to 5</td>
                                </tr>
                                <tr>
                                    <td style="background: #ccffcc;">5.1 to 8</td>
                                    <td style="background: #e6ffe6;">8.1 to 11</td>
                                    <td style="background: #ffffcc;">11.1 to 14</td>
                                    <td style="background: #fff099;">14.1 to 17</td>
                                    <td style="background: #ffeb3b;">17.1 to 20</td>
                                </tr>
                                <tr>
                                    <td style="background: #ffd700;">20.1 to 23</td>
                                    <td style="background: #ffcc80;">23.1 to 26</td>
                                    <td style="background: #ff9966;">26.1 to 29</td>
                                    <td style="background: #ff6666;">29.1 to 32</td>
                                    <td style="background: #ff3333;">> 32</td>
                                </tr>
                            </table>
                        </div>
                        <div class="gradient-key">
                            <h6 class="text-muted fs-14">Rainfall Key (mm)</h6>
                            <table>
                                <tr>
                                    <td style="background: transparent;">0 (Dry)</td>
                                    <td style="background: #e6f3ff;">0.1 to 2</td>
                                    <td style="background: #99ccff;">2.1 to 5</td>
                                    <td style="background: #3399ff;">5.1 to 10</td>
                                    <td style="background: #0066cc;">> 10 (Heavy)</td>
                                </tr>
                            </table>
                        </div>
                        <div class="gradient-key">
                            <h6 class="text-muted fs-14">Pressure Key (hPa)</h6>
                            <table>
                                <tr>
                                    <td style="background: #ff6666;">< 970 (Stormy)</td>
                                    <td style="background: #ffcc80;">970 to 990</td>
                                    <td style="background: #ccffcc;">990 to 1010</td>
                                    <td style="background: #99ebff;">1010 to 1030</td>
                                    <td style="background: #66d1f5;">> 1030 (Clear)</td>
                                </tr>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mt-4">No hourly weather data available.</p>
                    @endif

                    <!-- Debug Output -->
                    @if (config('app.debug'))
                        <pre class="text-left text-xs bg-light p-3 mt-4">
                            Debug: Current = {{ json_encode($weatherData['current'], JSON_PRETTY_PRINT) }}
                            Debug: Hourly = {{ json_encode($weatherData['hourly'], JSON_PRETTY_PRINT) }}
                            Debug: Sun/Moon = {{ json_encode($weatherData['sun'], JSON_PRETTY_PRINT) }}
                            Debug: Locations = {{ json_encode($locations->map(function($loc) {
                                return [
                                    'name' => $loc->name,
                                    'latitude' => $loc->latitude,
                                    'longitude' => $loc->longitude,
                                    'type' => $loc->type
                                ];
                            })->toArray(), JSON_PRETTY_PRINT) }}
                        </pre>
                    @endif

                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-light mt-3">Back to Dashboard</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.min.js" crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            try {
                // Leaflet Map
                console.log('Initializing Leaflet map');
                const map = L.map('leaflet-map').setView([{{ $location->latitude ?? 0 }}, {{ $location->longitude ?? 0 }}], 10);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                map.invalidateSize();

                L.marker([{{ $location->latitude ?? 0 }}, {{ $location->longitude ?? 0 }}], {
                    icon: L.icon({
                        iconUrl: 'https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/images/marker-icon.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34]
                    })
                }).addTo(map)
                  .bindPopup('<b>{{ addslashes($location->name) }}</b>')
                  .openPopup();

                const locations = @json($locations->filter(function($loc) {
                    return is_numeric($loc->latitude) && is_numeric($loc->longitude);
                })->map(function($loc) {
                    return [
                        'name' => $loc->name,
                        'latitude' => (float) $loc->latitude,
                        'longitude' => (float) $loc->longitude,
                        'type' => $loc->type
                    ];
                })->values()->toArray());
                console.log('Locations data:', locations);
                locations.forEach(loc => {
                    if (loc.name !== '{{ addslashes($location->name) }}') {
                        const isMarine = loc.type === 'Marine';
                        const url = isMarine ? '{{ route('marine.show', ':name') }}' : '{{ route('location.show', ':name') }}';
                        const safeName = encodeURIComponent(loc.name);
                        L.marker([loc.latitude, loc.longitude], {
                            icon: L.icon({
                                iconUrl: 'https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/images/marker-icon.png',
                                iconSize: [25, 41],
                                iconAnchor: [12, 41],
                                popupAnchor: [1, -34]
                            })
                        }).addTo(map)
                          .bindPopup(`<b><a href="${url.replace(':name', safeName)}">${loc.name}</a></b>`);
                    }
                });

                // Charts
                @if (!empty($weatherData['hourly']))
                    console.log('Initializing charts');
                    const chartLabels = [@foreach ($weatherData['hourly'] as $date => $hours)
                        '{{ \Carbon\Carbon::parse($date)->format('M d') }}'@if (!$loop->last),@endif
                    @endforeach];
                    console.log('Chart labels:', chartLabels);

                    const tempData = [@foreach ($weatherData['hourly'] as $date => $hours)
                        {{ $hours[0]['temperature'] ?? 'null' }}@if (!$loop->last),@endif
                    @endforeach];
                    console.log('Temperature data:', tempData);

                    const rainData = [@foreach ($weatherData['hourly'] as $date => $hours)
                        {{ collect($hours)->sum('precipitation') }}@if (!$loop->last),@endif
                    @endforeach];
                    console.log('Rainfall data:', rainData);

                    const gustData = [@foreach ($weatherData['hourly'] as $date => $hours)
                        {{ collect($hours)->max('wind_gust') ?? 'null' }}@if (!$loop->last),@endif
                    @endforeach];
                    console.log('Wind gust data:', gustData);

                    const tempCtx = document.getElementById('temperatureChart').getContext('2d');
                    new Chart(tempCtx, {
                        type: 'line',
                        data: {
                            labels: chartLabels,
                            datasets: [{
                                label: 'Temperature (°C)',
                                data: tempData,
                                borderColor: '#e74c3c',
                                backgroundColor: 'rgba(231, 76, 60, 0.2)',
                                fill: true,
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: { title: { display: true, text: 'Temperature (°C)' } },
                                x: { title: { display: true, text: 'Date' } }
                            },
                            plugins: { legend: { display: true, position: 'top' } }
                        }
                    });

                    const rainCtx = document.getElementById('rainfallChart').getContext('2d');
                    new Chart(rainCtx, {
                        type: 'bar',
                        data: {
                            labels: chartLabels,
                            datasets: [{
                                label: 'Rainfall Total (mm)',
                                data: rainData,
                                backgroundColor: '#3498db',
                                borderColor: '#2980b9',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: { title: { display: true, text: 'Rainfall (mm)' }, beginAtZero: true },
                                x: { title: { display: true, text: 'Date' } }
                            },
                            plugins: { legend: { display: true, position: 'top' } }
                        }
                    });

                    const gustCtx = document.getElementById('windGustChart').getContext('2d');
                    new Chart(gustCtx, {
                        type: 'bar',
                        data: {
                            labels: chartLabels,
                            datasets: [{
                                label: 'Peak Wind Gust (m/s)',
                                data: gustData,
                                backgroundColor: '#2ecc71',
                                borderColor: '#27ae60',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: { title: { display: true, text: 'Wind Gust (m/s)' }, beginAtZero: true },
                                x: { title: { display: true, text: 'Date' } }
                            },
                            plugins: { legend: { display: true, position: 'top' } }
                        }
                    });
                @endif
            } catch (e) {
                console.error('Error initializing map or charts:', e);
            }
        });
    </script>
@endsection