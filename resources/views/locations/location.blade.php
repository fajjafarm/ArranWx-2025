@extends('layouts.vertical', ['title' => $location->name . ' Weather'])

@section('html-attribute')
    data-sidenav-size="full"
@endsection

@section('css')
    @vite(['node_modules/flatpickr/dist/flatpickr.min.css'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .wave-graphic { display: flex; align-items: flex-end; gap: 10px; height: 100px; margin-top: 20px; }
        .wave-bar { width: 20px; background-color: #3498db; transition: height 0.3s; }
        .wave-label { text-align: center; font-size: 12px; color: #666; }
        .wave-direction { width: 50px; height: 50px; position: relative; margin: 20px auto; }
        .wave-arrow { width: 0; height: 0; border-left: 10px solid transparent; border-right: 10px solid transparent; border-bottom: 30px solid #e74c3c; position: absolute; top: 50%; left: 50%; transform-origin: center bottom; }
        .forecast-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .forecast-table th, .forecast-table td { padding: 10px; text-align: center; border-bottom: 1px solid #ddd; }
        .forecast-table th { background-color: #f8f9fa; font-weight: bold; }
        @media (max-width: 768px) {
            .forecast-table th, .forecast-table td { padding: 8px; font-size: 14px; }
            .forecast-table { display: block; overflow-x: auto; white-space: nowrap; }
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
                    <h4 class="text-muted fs-13 text-uppercase" title="{{ $location->name }}">
                        {{ $location->name }} ({{ $location->altitude ?? 0 }}m) - Current Conditions
                    </h4>
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
                            <span class="fw-semibold">Wave Direction:</span> {{ $weatherData['marine']['wave_direction'] ? $controller->degreesToCardinal($weatherData['marine']['wave_direction']) : 'N/A' }}
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
                            <span class="fw-semibold">Swell Direction:</span> {{ $weatherData['marine']['swell_wave_direction'] ? $controller->degreesToCardinal($weatherData['marine']['swell_wave_direction']) : 'N/A' }}
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

                        <!-- 3-Day Marine Forecast Chart -->
                        @if (!empty($weatherData['marine_forecast']))
                            <h6 class="text-muted fs-14 mt-4">3-Day Marine Forecast - Maximum Wave Height</h6>
                            <canvas id="marineWaveHeightChart" style="max-height: 200px; margin: 20px auto;"></canvas>
                        @endif

                        <!-- 3-Day Marine Hourly Forecast Table -->
                        @if (!empty($weatherData['marine_hourly']))
                            <h6 class="text-muted fs-14 mt-4">Hourly Marine Forecast (May 27–29, 2025)</h6>
                            <table class="forecast-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Time (BST)</th>
                                        <th>Wave Height (m)</th>
                                        <th>Sea Surface Temp (°C)</th>
                                        <th>Sea Level Height (m)</th>
                                        <th>Wave Direction</th>
                                        <th>Wave Period (s)</th>
                                        <th>Wind Wave Height (m)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($weatherData['marine_hourly'] as $hour)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($hour['time'])->setTimezone('Europe/London')->format('M d') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($hour['time'])->setTimezone('Europe/London')->format('H:i') }}</td>
                                            <td>{{ number_format($hour['wave_height'], 2) }}</td>
                                            <td>{{ number_format($hour['sea_surface_temperature'], 1) }}</td>
                                            <td>{{ number_format($hour['sea_level_height_msl'], 2) }}</td>
                                            <td>{{ $hour['wave_direction'] ? $controller->degreesToCardinal($hour['wave_direction']) : 'N/A' }}</td>
                                            <td>{{ number_format($hour['wave_period'], 2) }}</td>
                                            <td>{{ number_format($hour['wind_wave_height'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    @endif

                    <!-- 10-Day Forecast Table -->
                    @if (!empty($weatherData['hourly']))
                        <h6 class="text-muted fs-14 mt-4">10-Day Forecast</h6>
                        <table class="forecast-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Temp (°C)</th>
                                    <th>Wind (m/s)</th>
                                    <th>Humidity (%)</th>
                                    <th>Sunrise</th>
                                    <th>Sunset</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($weatherData['hourly'] as $date => $hours)
                                    @foreach ($hours as $hour)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($date)->format('M d') }}</td>
                                            <td>{{ $hour['temperature'] ?? 'N/A' }}</td>
                                            <td>{{ $hour['wind_speed'] ?? 'N/A' }}</td>
                                            <td>{{ $hour['humidity'] ?? 'N/A' }}</td>
                                            <td>{{ isset($weatherData['sun'][$date]['sunrise']) ? \Carbon\Carbon::parse($weatherData['sun'][$date]['sunrise'])->format('H:i') : 'N/A' }}</td>
                                            <td>{{ isset($weatherData['sun'][$date]['sunset']) ? \Carbon\Carbon::parse($weatherData['sun'][$date]['sunset'])->format('H:i') : 'N/A' }}</td>
                                        </tr>
                                        @break
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted mt-4">No forecast data available.</p>
                    @endif

                    <!-- Debug Output -->
                    <pre style="text-align: left; font-size: 12px; background: #f8f9fa; padding: 10px; margin-top: 20px;">
                        Debug: Current = {{ json_encode($weatherData['current'], JSON_PRETTY_PRINT) }}
                        Debug: Hourly = {{ json_encode($weatherData['hourly'], JSON_PRETTY_PRINT) }}
                        Debug: Marine = {{ json_encode($weatherData['marine'], JSON_PRETTY_PRINT) }}
                        Debug: Marine Forecast = {{ json_encode($weatherData['marine_forecast'], JSON_PRETTY_PRINT) }}
                        Debug: Marine Hourly = {{ json_encode($weatherData['marine_hourly'], JSON_PRETTY_PRINT) }}
                    </pre>

                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-light mt-3">Back to Dashboard</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @vite(['resources/js/pages/dashboard-sales.js'])
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