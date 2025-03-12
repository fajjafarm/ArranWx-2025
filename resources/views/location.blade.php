@extends('layouts.vertical', ['title' => $location->name . ' Weather'])

@section('html-attribute')
    data-sidenav-size="full"
@endsection

@section('css')
    @vite(['node_modules/flatpickr/dist/flatpickr.min.css'])
    <!-- Weather Icons CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/weather-icons/2.0.10/css/weather-icons.min.css">
    <style>
        .wave-graphic { display: flex; align-items: flex-end; gap: 10px; height: 100px; margin-top: 20px; }
        .wave-bar { width: 20px; background-color: #3498db; transition: height 0.3s; }
        .wave-label { text-align: center; font-size: 12px; color: #666; }
        .wave-direction { width: 50px; height: 50px; position: relative; margin: 20px auto; }
        .wave-arrow { width: 0; height: 0; border-left: 10px solid transparent; border-right: 10px solid transparent; border-bottom: 30px solid #e74c3c; position: absolute; top: 50%; left: 50%; transform-origin: center bottom; }
        .hourly-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .hourly-table th, .hourly-table td { padding: 8px; text-align: center; border-bottom: 1px solid #ddd; font-size: 14px; }
        .hourly-table th { background-color: #f8f9fa; font-weight: bold; }
        .day-header { background-color: #e9ecef; font-size: 16px; padding: 10px; text-align: left; margin-top: 20px; }
        .weather-icon { font-size: 24px; color: #555; }
        .sun-moon-icon { font-size: 18px; color: #777; margin-right: 5px; vertical-align: middle; }
        .day-header span { margin-right: 15px; }
        .beaufort-key, .gradient-key { margin-top: 20px; padding: 10px; background: #f8f9fa; border-radius: 5px; }
        .beaufort-key table, .gradient-key table { width: 100%; border-collapse: collapse; }
        .beaufort-key td, .gradient-key td { padding: 5px; text-align: center; font-size: 12px; }
        @media (max-width: 768px) {
            .hourly-table { display: block; overflow-x: auto; white-space: nowrap; }
            .hourly-table th, .hourly-table td { padding: 6px; font-size: 12px; }
            .weather-icon { font-size: 18px; }
            .sun-moon-icon { font-size: 14px; margin-right: 3px; }
            .day-header { font-size: 14px; }
            .day-header span { margin-right: 10px; }
            .beaufort-key td, .gradient-key td { font-size: 10px; padding: 3px; }
        }
    </style>
@endsection

@section('content')
    @include('layouts.partials.page-title', ['subtitle' => 'Weather Details', 'title' => $location->name])

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body text-center">
                    <!-- Current Conditions -->
                    <h5 class="text-muted fs-13 text-uppercase" title="{{ $location->name }}">
                        {{ $location->name }} ({{ $location->altitude ?? 0 }}m) - Current Conditions
                    </h5>
                    <div class="d-flex align-items-center justify-content-center gap-2 my-2 py-1">
                        <div class="user-img fs-42 flex-shrink-0">
                            <span class="avatar-title {{ $weatherData['type'] === 'Hill' ? 'text-bg-success' : ($weatherData['type'] === 'Marine' ? 'text-bg-info' : 'text-bg-primary') }} rounded-circle fs-22">
                                <iconify-icon icon="{{ $weatherData['type'] === 'Hill' ? 'solar:mountains-bold-duotone' : ($weatherData['type'] === 'Marine' ? 'solar:water-bold-duotone' : 'solar:buildings-bold-duotone') }}"></iconify-icon>
                            </span>
                        </div>
                        <h3 class="mb-0 fw-bold">
                            @if (isset($weatherData['current']['air_temperature']))
                                {{ $weatherData['current']['air_temperature'] }}°C
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
                    @if ($weatherData['type'] === 'Marine' && isset($weatherData['marine']))
                        <hr class="my-2">
                        <h6 class="text-muted fs-14">Current Marine Conditions</h6>
                        <p class="mb-1 text-muted fs-12">
                            <span class="fw-semibold">Sea Surface Temp:</span> {{ $weatherData['marine']['sea_surface_temperature'] ?? 'N/A' }}°C
                        </p>
                        <p class="mb-1 text-muted fs-12">
                            <span class="fw-semibold">Wave Height:</span> {{ $weatherData['marine']['wave_height'] ?? 'N/A' }} m
                        </p>
                        <p class="mb-1 text-muted fs-12">
                            <span class="fw-semibold">Wave Direction:</span> {{ $weatherData['marine']['wave_direction'] ?? 'N/A' }}°
                        </p>
                        <p class="mb-1 text-muted fs-12">
                            <span class="fw-semibold">Wave Period:</span> {{ $weatherData['marine']['wave_period'] ?? 'N/A' }} s
                        </p>
                        <p class="mb-1 text-muted fs-12">
                            <span class="fw-semibold">Wind Wave Height:</span> {{ $weatherData['marine']['wind_wave_height'] ?? 'N/A' }} m
                        </p>
                        <p class="mb-1 text-muted fs-12">
                            <span class="fw-semibold">Swell Height:</span> {{ $weatherData['marine']['swell_wave_height'] ?? 'N/A' }} m
                        </p>
                        <p class="mb-1 text-muted fs-12">
                            <span class="fw-semibold">Swell Direction:</span> {{ $weatherData['marine']['swell_wave_direction'] ?? 'N/A' }}°
                        </p>
                        <p class="mb-1 text-muted fs-12">
                            <span class="fw-semibold">Swell Period:</span> {{ $weatherData['marine']['swell_wave_period'] ?? 'N/A' }} s
                        </p>
                        <div class="wave-graphic">
                            <div>
                                <div class="wave-bar" style="height: {{ ($weatherData['marine']['wave_height'] ?? 0) * 20 }}px;"></div>
                                <div class="wave-label">Wave</div>
                            </div>
                            <div>
                                <div class="wave-bar" style="height: {{ ($weatherData['marine']['wind_wave_height'] ?? 0) * 20 }}px;"></div>
                                <div class="wave-label">Wind Wave</div>
                            </div>
                            <div>
                                <div class="wave-bar" style="height: {{ ($weatherData['marine']['swell_wave_height'] ?? 0) * 20 }}px;"></div>
                                <div class="wave-label">Swell</div>
                            </div>
                        </div>
                        @if (isset($weatherData['marine']['wave_direction']))
                            <div class="wave-direction">
                                <div class="wave-arrow" style="transform: translate(-50%, -50%) rotate({{ $weatherData['marine']['wave_direction'] }}deg);"></div>
                            </div>
                        @endif
                    @endif

                    <!-- Hourly Weather Data (Villages and Hills) -->
                    @if (in_array($weatherData['type'], ['Village', 'Hill']) && isset($weatherData['hourly']) && count($weatherData['hourly']) > 0)
                        <h6 class="text-muted fs-14 mt-4">Hourly Weather Forecast (Every 2 Hours)</h6>
                        @foreach ($weatherData['hourly'] as $date => $hours)
                            <?php
                                // Min/Max for vertical flow
                                $windSpeeds = array_filter(array_column($hours, 'wind_speed'), 'is_numeric');
                                $windGusts = array_filter(array_column($hours, 'wind_gust'), 'is_numeric');
                                $temps = array_filter(array_column($hours, 'temperature'), 'is_numeric');
                                $rains = array_filter(array_column($hours, 'precipitation'), 'is_numeric');
                                $pressures = array_filter(array_column($hours, 'pressure'), 'is_numeric');

                                $minWind = !empty($windSpeeds) ? min($windSpeeds) : 0;
                                $maxWind = !empty($windSpeeds) ? max($windSpeeds) : 0;
                                $minGust = !empty($windGusts) ? min($windGusts) : 0;
                                $maxGust = !empty($windGusts) ? max($windGusts) : 0;
                                $minTemp = !empty($temps) ? min($temps) : -10;
                                $maxTemp = !empty($temps) ? max($temps) : 30;
                                $minRain = !empty($rains) ? min($rains) : 0;
                                $maxRain = !empty($rains) ? max($rains) : 10;
                                $minPressure = !empty($pressures) ? min($pressures) : 970;
                                $maxPressure = !empty($pressures) ? max($pressures) : 1030;

                                // Beaufort Scale
                                $beaufortRanges = [
                                    0 => ['max' => 0.5, 'desc' => 'Calm', 'colorStart' => '#e6ffe6', 'colorEnd' => '#e6ffe6'],
                                    1 => ['max' => 1.5, 'desc' => 'Light Air', 'colorStart' => '#ccffcc', 'colorEnd' => '#b3ffb3'],
                                    2 => ['max' => 3.3, 'desc' => 'Light Breeze', 'colorStart' => '#b3ffb3', 'colorEnd' => '#99ff99'],
                                    3 => ['max' => 5.5, 'desc' => 'Gentle Breeze', 'colorStart' => '#99ff99', 'colorEnd' => '#80ff80'],
                                    4 => ['max' => 7.9, 'desc' => 'Moderate Breeze', 'colorStart' => '#80ff80', 'colorEnd' => '#66ff66'],
                                    5 => ['max' => 10.7, 'desc' => 'Fresh Breeze', 'colorStart' => '#ffff99', 'colorEnd' => '#ffeb3b'],
                                    6 => ['max' => 13.8, 'desc' => 'Strong Breeze', 'colorStart' => '#ffeb3b', 'colorEnd' => '#ffd700'],
                                    7 => ['max' => 17.1, 'desc' => 'Near Gale', 'colorStart' => '#ffcc80', 'colorEnd' => '#ff9800'],
                                    8 => ['max' => 20.7, 'desc' => 'Gale', 'colorStart' => '#ff9800', 'colorEnd' => '#ff6666'],
                                    9 => ['max' => 24.4, 'desc' => 'Strong Gale', 'colorStart' => '#ff6666', 'colorEnd' => '#ff3333'],
                                    10 => ['max' => 28.4, 'desc' => 'Storm', 'colorStart' => '#ff3333', 'colorEnd' => '#cc0000'],
                                    11 => ['max' => 32.6, 'desc' => 'Violent Storm', 'colorStart' => '#cc0000', 'colorEnd' => '#990000'],
                                    12 => ['max' => PHP_FLOAT_MAX, 'desc' => 'Hurricane Force', 'colorStart' => '#990000', 'colorEnd' => '#800000']
                                ];

                                // Temperature Scale (every 3°C)
                                $tempRanges = [
                                    ['min' => -10, 'max' => -7, 'colorStart' => '#00b7eb', 'colorEnd' => '#33c4f0'],
                                    ['min' => -6.9, 'max' => -4, 'colorStart' => '#33c4f0', 'colorEnd' => '#66d1f5'],
                                    ['min' => -3.9, 'max' => -1, 'colorStart' => '#66d1f5', 'colorEnd' => '#99ebff'],
                                    ['min' => -0.9, 'max' => 2, 'colorStart' => '#99ebff', 'colorEnd' => '#b3f0ff'],
                                    ['min' => 2.1, 'max' => 5, 'colorStart' => '#b3f0ff', 'colorEnd' => '#ccffcc'],
                                    ['min' => 5.1, 'max' => 8, 'colorStart' => '#ccffcc', 'colorEnd' => '#e6ffe6'],
                                    ['min' => 8.1, 'max' => 11, 'colorStart' => '#e6ffe6', 'colorEnd' => '#ffffcc'],
                                    ['min' => 11.1, 'max' => 14, 'colorStart' => '#ffffcc', 'colorEnd' => '#fff099'],
                                    ['min' => 14.1, 'max' => 17, 'colorStart' => '#fff099', 'colorEnd' => '#ffeb3b'],
                                    ['min' => 17.1, 'max' => 20, 'colorStart' => '#ffeb3b', 'colorEnd' => '#ffd700'],
                                    ['min' => 20.1, 'max' => 23, 'colorStart' => '#ffd700', 'colorEnd' => '#ffcc80'],
                                    ['min' => 23.1, 'max' => 26, 'colorStart' => '#ffcc80', 'colorEnd' => '#ff9966'],
                                    ['min' => 26.1, 'max' => 29, 'colorStart' => '#ff9966', 'colorEnd' => '#ff6666'],
                                    ['min' => 29.1, 'max' => 32, 'colorStart' => '#ff6666', 'colorEnd' => '#ff3333'],
                                    ['min' => 32.1, 'max' => PHP_FLOAT_MAX, 'colorStart' => '#ff3333', 'colorEnd' => '#cc0000']
                                ];
                            ?>
                            <div class="day-header">
                                {{ \Carbon\Carbon::parse($date)->format('l, M d') }}
                                <span><i class="wi wi-sunrise sun-moon-icon"></i> {{ isset($weatherData['sun_moon'][$date]['sunrise']) ? \Carbon\Carbon::parse($weatherData['sun_moon'][$date]['sunrise'])->format('H:i') : 'N/A' }}</span>
                                <span><i class="wi wi-sunset sun-moon-icon"></i> {{ isset($weatherData['sun_moon'][$date]['sunset']) ? \Carbon\Carbon::parse($weatherData['sun_moon'][$date]['sunset'])->format('H:i') : 'N/A' }}</span>
                                <span><i class="wi wi-moonrise sun-moon-icon"></i> {{ isset($weatherData['sun_moon'][$date]['moonrise']) ? \Carbon\Carbon::parse($weatherData['sun_moon'][$date]['moonrise'])->format('H:i') : 'N/A' }}</span>
                                <span><i class="wi wi-moonset sun-moon-icon"></i> {{ isset($weatherData['sun_moon'][$date]['moonset']) ? \Carbon\Carbon::parse($weatherData['sun_moon'][$date]['moonset'])->format('H:i') : 'N/A' }}</span>
                            </div>
                            <table class="hourly-table">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Temp (°C)</th>
                                        <th>Rain (mm)</th>
                                        <th>Condition</th>
                                        <th>Wind (m/s)</th>
                                        <th>Gusts (m/s)</th>
                                        <th>Pressure (hPa)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($hours as $hour)
                                        <?php
                                            $condition = $hour['condition'];
                                            $isNight = strpos($condition, '_night') !== false;
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

                                            // Wind Speed Gradient
                                            $windSpeed = $hour['wind_speed'] ?? 0;
                                            $windBeaufortLevel = 0;
                                            $windBeaufortDesc = 'N/A';
                                            $windGradient = 'background: #e6ffe6;';
                                            if (is_numeric($windSpeed)) {
                                                foreach ($beaufortRanges as $level => $range) {
                                                    if ($windSpeed <= $range['max']) {
                                                        $windBeaufortLevel = $level;
                                                        $windBeaufortDesc = $range['desc'];
                                                        $windGradient = "background: linear-gradient(to bottom, {$range['colorStart']}, {$range['colorEnd']});";
                                                        break;
                                                    }
                                                }
                                            }

                                            // Wind Gust Gradient
                                            $windGust = $hour['wind_gust'] ?? 0;
                                            $gustBeaufortLevel = 0;
                                            $gustBeaufortDesc = 'N/A';
                                            $gustGradient = 'background: #e6ffe6;';
                                            if (is_numeric($windGust)) {
                                                foreach ($beaufortRanges as $level => $range) {
                                                    if ($windGust <= $range['max']) {
                                                        $gustBeaufortLevel = $level;
                                                        $gustBeaufortDesc = $range['desc'];
                                                        $gustGradient = "background: linear-gradient(to bottom, {$range['colorStart']}, {$range['colorEnd']});";
                                                        break;
                                                    }
                                                }
                                            }

                                            // Temperature Gradient
                                            $temp = $hour['temperature'] ?? null;
                                            $tempGradient = 'background: #e6ffe6;';
                                            if (is_numeric($temp)) {
                                                foreach ($tempRanges as $range) {
                                                    if ($temp >= $range['min'] && $temp <= $range['max']) {
                                                        $tempGradient = "background: linear-gradient(to bottom, {$range['colorStart']}, {$range['colorEnd']});";
                                                        break;
                                                    }
                                                }
                                            }

                                            // Rainfall Gradient
                                            $rain = $hour['precipitation'] ?? 0;
                                            $rainGradient = 'background: #e6ffe6;';
                                            if (is_numeric($rain)) {
                                                if ($rain <= 0) $rainGradient = 'background: linear-gradient(to bottom, #e6ffe6, #e6ffe6);';
                                                elseif ($rain <= 2) $rainGradient = 'background: linear-gradient(to bottom, #e6ffe6, #99ebff);';
                                                elseif ($rain <= 5) $rainGradient = 'background: linear-gradient(to bottom, #99ebff, #00b7eb);';
                                                elseif ($rain <= 10) $rainGradient = 'background: linear-gradient(to bottom, #00b7eb, #0066cc);';
                                                else $rainGradient = 'background: linear-gradient(to bottom, #0066cc, #0066cc);';
                                            }

                                            // Pressure Gradient
                                            $pressure = $hour['pressure'] ?? null;
                                            $pressureGradient = 'background: #e6ffe6;';
                                            if (is_numeric($pressure)) {
                                                if ($pressure <= 970) $pressureGradient = 'background: linear-gradient(to bottom, #ff6666, #ff6666);';
                                                elseif ($pressure <= 990) $pressureGradient = 'background: linear-gradient(to bottom, #ff6666, #ffcc80);';
                                                elseif ($pressure <= 1010) $pressureGradient = 'background: linear-gradient(to bottom, #ffcc80, #ccffcc);';
                                                elseif ($pressure <= 1030) $pressureGradient = 'background: linear-gradient(to bottom, #ccffcc, #99ebff);';
                                                else $pressureGradient = 'background: linear-gradient(to bottom, #99ebff, #99ebff);';
                                            }
                                        ?>
                                        <tr>
                                            <td>{{ $hour['time'] }}</td>
                                            <td style="{{ $tempGradient }}">{{ $hour['temperature'] ?? 'N/A' }}</td>
                                            <td style="{{ $rainGradient }}">{{ $hour['precipitation'] ?? 'N/A' }}</td>
                                            <td><i class="wi {{ $iconClass }} weather-icon" title="{{ $condition }}"></i></td>
                                            <td style="{{ $windGradient }}" title="{{ $windBeaufortLevel }}: {{ $windBeaufortDesc }}">{{ $hour['wind_speed'] ?? 'N/A' }}</td>
                                            <td style="{{ $gustGradient }}" title="{{ $gustBeaufortLevel }}: {{ $gustBeaufortDesc }}">{{ $hour['wind_gust'] ?? 'N/A' }}{{ !isset($hour['wind_gust']) && isset($hour['wind_speed']) ? '*' : '' }}</td>
                                            <td style="{{ $pressureGradient }}">{{ $hour['pressure'] ?? 'N/A' }}</td>
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
                                    <td style="background: linear-gradient(to bottom, #e6ffe6, #e6ffe6);">0: Calm (< 0.5 m/s)</td>
                                    <td style="background: linear-gradient(to bottom, #ccffcc, #b3ffb3);">1: Light Air (0.5–1.5 m/s)</td>
                                    <td style="background: linear-gradient(to bottom, #b3ffb3, #99ff99);">2: Light Breeze (1.6–3.3 m/s)</td>
                                    <td style="background: linear-gradient(to bottom, #99ff99, #80ff80);">3: Gentle Breeze (3.4–5.5 m/s)</td>
                                </tr>
                                <tr>
                                    <td style="background: linear-gradient(to bottom, #80ff80, #66ff66);">4: Moderate Breeze (5.6–7.9 m/s)</td>
                                    <td style="background: linear-gradient(to bottom, #ffff99, #ffeb3b);">5: Fresh Breeze (8.0–10.7 m/s)</td>
                                    <td style="background: linear-gradient(to bottom, #ffeb3b, #ffd700);">6: Strong Breeze (10.8–13.8 m/s)</td>
                                    <td style="background: linear-gradient(to bottom, #ffcc80, #ff9800);">7: Near Gale (13.9–17.1 m/s)</td>
                                </tr>
                                <tr>
                                    <td style="background: linear-gradient(to bottom, #ff9800, #ff6666);">8: Gale (17.2–20.7 m/s)</td>
                                    <td style="background: linear-gradient(to bottom, #ff6666, #ff3333);">9: Strong Gale (20.8–24.4 m/s)</td>
                                    <td style="background: linear-gradient(to bottom, #ff3333, #cc0000);">10: Storm (24.5–28.4 m/s)</td>
                                    <td style="background: linear-gradient(to bottom, #cc0000, #990000);">11: Violent Storm (28.5–32.6 m/s)</td>
                                </tr>
                                <tr>
                                    <td colspan="4" style="background: linear-gradient(to bottom, #990000, #800000);">12: Hurricane Force (> 32.6 m/s)</td>
                                </tr>
                            </table>
                        </div>

                        <!-- Additional Gradient Keys -->
                        <div class="gradient-key">
                            <h6 class="text-muted fs-14">Temperature Key (°C)</h6>
                            <table>
                                <tr>
                                    <td style="background: linear-gradient(to bottom, #00b7eb, #33c4f0);">-10 to -7</td>
                                    <td style="background: linear-gradient(to bottom, #33c4f0, #66d1f5);">-7 to -4</td>
                                    <td style="background: linear-gradient(to bottom, #66d1f5, #99ebff);">-4 to -1</td>
                                    <td style="background: linear-gradient(to bottom, #99ebff, #b3f0ff);">-1 to 2</td>
                                    <td style="background: linear-gradient(to bottom, #b3f0ff, #ccffcc);">2.1 to 5</td>
                                </tr>
                                <tr>
                                    <td style="background: linear-gradient(to bottom, #ccffcc, #e6ffe6);">5.1 to 8</td>
                                    <td style="background: linear-gradient(to bottom, #e6ffe6, #ffffcc);">8.1 to 11</td>
                                    <td style="background: linear-gradient(to bottom, #ffffcc, #fff099);">11.1 to 14</td>
                                    <td style="background: linear-gradient(to bottom, #fff099, #ffeb3b);">14.1 to 17</td>
                                    <td style="background: linear-gradient(to bottom, #ffeb3b, #ffd700);">17.1 to 20</td>
                                </tr>
                                <tr>
                                    <td style="background: linear-gradient(to bottom, #ffd700, #ffcc80);">20.1 to 23</td>
                                    <td style="background: linear-gradient(to bottom, #ffcc80, #ff9966);">23.1 to 26</td>
                                    <td style="background: linear-gradient(to bottom, #ff9966, #ff6666);">26.1 to 29</td>
                                    <td style="background: linear-gradient(to bottom, #ff6666, #ff3333);">29.1 to 32</td>
                                    <td style="background: linear-gradient(to bottom, #ff3333, #cc0000);">> 32</td>
                                </tr>
                            </table>
                        </div>
                        <div class="gradient-key">
                            <h6 class="text-muted fs-14">Rainfall Key (mm)</h6>
                            <table>
                                <tr>
                                    <td style="background: linear-gradient(to bottom, #e6ffe6, #e6ffe6);">0 (Dry)</td>
                                    <td style="background: linear-gradient(to bottom, #e6ffe6, #99ebff);">0 to 2</td>
                                    <td style="background: linear-gradient(to bottom, #99ebff, #00b7eb);">2 to 5</td>
                                    <td style="background: linear-gradient(to bottom, #00b7eb, #0066cc);">5 to 10</td>
                                    <td style="background: linear-gradient(to bottom, #0066cc, #0066cc);">> 10 (Heavy)</td>
                                </tr>
                            </table>
                        </div>
                        <div class="gradient-key">
                            <h6 class="text-muted fs-14">Pressure Key (hPa)</h6>
                            <table>
                                <tr>
                                    <td style="background: linear-gradient(to bottom, #ff6666, #ff6666);">< 970 (Stormy)</td>
                                    <td style="background: linear-gradient(to bottom, #ff6666, #ffcc80);">970 to 990</td>
                                    <td style="background: linear-gradient(to bottom, #ffcc80, #ccffcc);">990 to 1010</td>
                                    <td style="background: linear-gradient(to bottom, #ccffcc, #99ebff);">1010 to 1030</td>
                                    <td style="background: linear-gradient(to bottom, #99ebff, #99ebff);">> 1030 (Clear)</td>
                                </tr>
                            </table>
                        </div>
                    @elseif ($weatherData['type'] !== 'Marine')
                        <p class="text-muted mt-4">No hourly weather data available.</p>
                    @endif

                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-light mt-3">Back to Dashboard</a>
                </div>
            </div>
        </div><!-- end col -->
    </div><!-- end row -->
@endsection

@section('scripts')
    @vite(['resources/js/pages/dashboard-sales.js'])
@endsection