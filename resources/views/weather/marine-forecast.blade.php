@extends('layouts.vertical', ['title' => $location->name . ' Marine Forecast'])

@section('html-attribute')
    data-sidenav-size="full"
@endsection

@section('css')
    @vite(['node_modules/flatpickr/dist/flatpickr.min.css'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .wave-graphic {
            display: flex;
            align-items: flex-end;
            gap: 10px;
            height: 100px;
            margin-top: 20px;
            justify-content: center;
        }
        .wave-bar {
            width: 20px;
            background-color: #3498db;
            transition: height 0.3s;
        }
        .wave-label {
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .wave-direction {
            width: 50px;
            height: 50px;
            position: relative;
            margin: 20px auto;
        }
        .wave-arrow {
            width: 0;
            height: 0;
            border-left: 10px solid transparent;
            border-right: 10px solid transparent;
            border-bottom: 30px solid #e74c3c;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(0deg);
            transform-origin: center bottom;
        }
        .forecast-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .forecast-table th,
        .forecast-table td {
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        .forecast-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .daily-header {
            background-color: #e9ecef;
            font-size: 14px;
        }
        @media (max-width: 768px) {
            .forecast-table th,
            .forecast-table td {
                padding: 8px;
                font-size: 14px;
            }
            .forecast-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }
    </style>
@endsection

@section('content')
    @include('layouts.partials.page-title', ['subtitle' => 'Marine Weather Details', 'title' => $location->name])

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body text-center">
                    <!-- Header: Map, Title, Description -->
                    <div class="row mb-4">
                        <div class="col-md-3 text-center">
                            <img src="https://api.mapbox.com/styles/v1/mapbox/streets-v11/static/pin-s+ff0000({{ $location->longitude }},{{ $location->latitude }})/{{ $location->longitude }},{{ $location->latitude }},12,0/150x150?access_token={{ env('MAPBOX_ACCESS_TOKEN') }}"
                                 alt="Map of {{ $location->name }}"
                                 class="img-fluid rounded">
                        </div>
                        <div class="col-md-9 text-start">
                            <h2 class="card-title">{{ $location->name }} Marine Forecast</h2>
                            <p class="text-muted">
                                {{ $location->description ?? 'Marine forecast for ' . $location->name . ' at sea.' }}
                            </p>
                        </div>
                    </div>

                    <!-- Current Conditions -->
                    <h4 class="text-muted fs-13 text-uppercase">
                        {{ $location->name }} - Current Conditions
                    </h4>
                    <div class="d-flex align-items-center justify-content-center gap-2 my-2 py-1">
                        <div class="user-img fs-42 flex-shrink-0">
                            <span class="avatar-title text-bg-info rounded-circle fs-22">
                                <iconify-icon icon="solar:water-bold-duotone"></iconify-icon>
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

                    <!-- Current Marine Conditions -->
                    @if (isset($weatherData['marine']))
                        <hr class="my-2">
                        <h6 class="text-muted fs-14">Current Marine Conditions</h6>
                        <div class="row text-muted fs-12">
                            <div class="col-md-3">
                                <p><span class="fw-semibold">Sea Surface Temp:</span> {{ $weatherData['marine']['sea_surface_temperature'] ?? 'N/A' }}°C</p>
                                <p><span class="fw-semibold">Wave Height:</span> {{ $weatherData['marine']['wave_height'] ?? 'N/A' }} m</p>
                            </div>
                            <div class="col-md-3">
                                <p><span class="fw-semibold">Wave Direction:</span> {{ $weatherData['marine']['wave_direction'] ? $controller->degreesToCardinal($weatherData['marine']['wave_direction']) : 'N/A' }}</p>
                                <p><span class="fw-semibold">Wave Period:</span> {{ $weatherData['marine']['wave_period'] ?? 'N/A' }} s</p>
                            </div>
                            <div class="col-md-3">
                                <p><span class="fw-semibold">Wind Wave Height:</span> {{ $weatherData['marine']['wind_wave_height'] ?? 'N/A' }} m</p>
                                <p><span class="fw-semibold">Swell Height:</span> {{ $weatherData['marine']['swell_wave_height'] ?? 'N/A' }} m</p>
                            </div>
                            <div class="col-md-3">
                                <p><span class="fw-semibold">Sea Level Height:</span> {{ $weatherData['marine']['sea_level_height_msl'] ?? 'N/A' }} m</p>
                            </div>
                        </div>
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

                    <!-- 7-Day Marine Forecast Chart -->
                    @if (!empty($weatherData['marine_forecast']))
                        <h6 class="text-muted fs-14 mt-4">7-Day Marine Forecast - Maximum Wave Height</h6>
                        <canvas id="marineWaveHeightChart" style="max-height: 200px; margin: 20px auto;"></canvas>
                    @endif

                    <!-- 7-Day Marine Hourly Forecast Table -->
                    @if (!empty($weatherData['marine_hourly']))
                        <h6 class="text-muted fs-14 mt-4">7-Day Hourly Marine Forecast</h6>
                        <table class="forecast-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Time (BST)</th>
                                    <th>Wave Height (m)</th>
                                    <th>Sea Temp (°C)</th>
                                    <th>Sea Level (m)</th>
                                    <th>Wave Dir</th>
                                    <th>Wave Period (s)</th>
                                    <th>Wind Wave (m)</th>
                                    <th>Swell (m)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($weatherData['marine_hourly'] as $hour)
                                    @php
                                        $date = \Carbon\Carbon::parse($hour['time'])->format('Y-m-d');
                                        $dailyData = collect($weatherData['daily_marine_data'])->firstWhere('date', $date);
                                    @endphp
                                    @if ($loop->first || \Carbon\Carbon::parse($hour['time'])->format('Y-m-d') !== \Carbon\Carbon::parse($weatherData['marine_hourly'][$loop->index - 1]['time'])->format('Y-m-d'))
                                        <tr class="daily-header">
                                            <td colspan="9">
                                                <strong>{{ \Carbon\Carbon::parse($date)->format('l, d M Y') }}</strong><br>
                                                Sunrise: {{ $weatherData['sun'][$date]['sunrise'] ?? 'N/A' }} |
                                                Sunset: {{ $weatherData['sun'][$date]['sunset'] ?? 'N/A' }} |
                                                Moonrise: {{ $weatherData['sun'][$date]['moonrise'] ?? 'N/A' }} |
                                                Moonset: {{ $weatherData['sun'][$date]['moonset'] ?? 'N/A' }}<br>
                                                Max Wave Height: {{ $dailyData['wave_height_max'] ?? 'N/A' }} m |
                                                Dominant Wave Dir: {{ $dailyData['wave_direction_dominant'] ? $controller->degreesToCardinal($dailyData['wave_direction_dominant']) : 'N/A' }}
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($hour['time'])->setTimezone('Europe/London')->format('M d') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($hour['time'])->setTimezone('Europe/London')->format('H:i') }}</td>
                                        <td>{{ number_format($hour['wave_height'], 2) }}</td>
                                        <td>{{ number_format($hour['sea_surface_temperature'], 1) }}</td>
                                        <td>{{ number_format($hour['sea_level_height_msl'], 2) }}</td>
                                        <td>{{ $hour['wave_direction'] ? $controller->degreesToCardinal($hour['wave_direction']) : 'N/A' }}</td>
                                        <td>{{ number_format($hour['wave_period'], 2) }}</td>
                                        <td>{{ number_format($hour['wind_wave_height'], 2) }}</td>
                                        <td>{{ number_format($hour['swell_wave_height'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                    <!-- Debug Output -->
                    @if (config('app.debug'))
                        <pre class="text-left text-xs bg-light p-3 mt-4">
                            Debug: Marine API URL = {{ $weatherData['marine_api_url'] ?? 'N/A' }}
                            Debug: Current = {{ json_encode($weatherData['current'], JSON_PRETTY_PRINT) }}
                            Debug: Marine = {{ json_encode($weatherData['marine'], JSON_PRETTY_PRINT) }}
                            Debug: Marine Forecast = {{ json_encode($weatherData['marine_forecast'], JSON_PRETTY_PRINT) }}
                            Debug: Marine Hourly = {{ json_encode($weatherData['marine_hourly'], JSON_PRETTY_PRINT) }}
                            Debug: Daily Marine Data = {{ json_encode($weatherData['daily_marine_data'], JSON_PRETTY_PRINT) }}
                        </pre>
                    @endif

                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-light mt-3">Back to Dashboard</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @vite(['resources/js/pages/dashboard-sales.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if (!empty($weatherData['marine_forecast']))
                const ctx = document.getElementById('marineWaveHeightChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: [
                            @foreach ($weatherData['marine_forecast'] as $day)
                                '{{ \Carbon\Carbon::parse($day['date'])->format('M d') }}',
                            @endforeach
                        ],
                        datasets: [{
                            label: 'Max Wave Height (m)',
                            data: [
                                @foreach ($weatherData['marine_forecast'] as $day)
                                    {{ $day['wave_height_max'] }},
                                @endforeach
                            ],
                            borderColor: '#3498db',
                            backgroundColor: 'rgba(52, 152, 219, 0.2)',
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Wave Height (m)'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Date'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            }
                        }
                    }
                });
            @endif
        });
    </script>
@endsection