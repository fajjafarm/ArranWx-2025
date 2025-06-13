@extends('layouts.vertical', ['title' => $location->name . ' Weather'])

@section('html-attribute')
    data-sidenav-size="full"
@endsection

@section('css')
    @vite(['node_modules/flatpickr/dist/flatpickr.min.css'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
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
    @include('layouts.partials.page-title', ['subtitle' => 'Weather Details', 'title' => $location->name])

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
                            <h2 class="card-title">{{ $location->name }} Weather Forecast</h2>
                            <p class="text-muted">
                                {{ $location->description ?? 'Weather forecast for ' . $location->name . ', located at latitude ' . $location->latitude . ', longitude ' . $location->longitude . ', altitude ' . ($location->altitude ?? 0) . ' meters above sea level.' }}
                            </p>
                        </div>
                    </div>

                    <!-- Current Conditions -->
                    <h4 class="text-muted fs-13 text-uppercase">
                        {{ $location->name }} ({{ $location->altitude ?? 0 }}m) - Current Conditions
                    </h4>
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

                    <!-- Temperature Chart -->
                    @if (!empty($weatherData['hourly']))
                        <h6 class="text-muted fs-14 mt-4">7-Day Temperature Forecast</h6>
                        <canvas id="temperatureChart" style="max-height: 200px; margin: 20px auto;"></canvas>
                    @endif

                    <!-- 7-Day Forecast Table -->
                    @if (!empty($weatherData['hourly']))
                        <h6 class="text-muted fs-14 mt-4">7-Day Weather Forecast</h6>
                        <table class="forecast-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Time (BST)</th>
                                    <th>Temp (°C)</th>
                                    <th>Wind (m/s)</th>
                                    <th>Wind Dir</th>
                                    <th>Precip (mm)</th>
                                    <th>Pressure (hPa)</th>
                                    <th>Condition</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($weatherData['hourly'] as $date => $hours)
                                    <tr class="daily-header">
                                        <td colspan="8">
                                            <strong>{{ \Carbon\Carbon::parse($date)->format('l, d M Y') }}</strong><br>
                                            Sunrise: {{ $weatherData['sun'][$date]['sunrise'] ?? 'N/A' }} |
                                            Sunset: {{ $weatherData['sun'][$date]['sunset'] ?? 'N/A' }} |
                                            Moonrise: {{ $weatherData['sun'][$date]['moonrise'] ?? 'N/A' }} |
                                            Moonset: {{ $weatherData['sun'][$date]['moonset'] ?? 'N/A' }}
                                        </td>
                                    </tr>
                                    @foreach ($hours as $hour)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($date)->format('M d') }}</td>
                                            <td>{{ $hour['time'] }}</td>
                                            <td>{{ $hour['temperature'] ?? 'N/A' }}</td>
                                            <td>{{ $hour['wind_speed'] ?? 'N/A' }}</td>
                                            <td>{{ $hour['wind_direction'] ?? 'N/A' }}</td>
                                            <td>{{ $hour['precipitation'] ?? 'N/A' }}</td>
                                            <td>{{ $hour['pressure'] ?? 'N/A' }}</td>
                                            <td>{{ $hour['condition'] ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted mt-4">No forecast data available.</p>
                    @endif

                    <!-- Debug Output -->
                    @if (config('app.debug'))
                        <pre class="text-left text-xs bg-light p-3 mt-4">
                            Debug: Current = {{ json_encode($weatherData['current'], JSON_PRETTY_PRINT) }}
                            Debug: Hourly = {{ json_encode($weatherData['hourly'], JSON_PRETTY_PRINT) }}
                            Debug: Sun/Moon = {{ json_encode($weatherData['sun'], JSON_PRETTY_PRINT) }}
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
            @if (!empty($weatherData['hourly']))
                const ctx = document.getElementById('temperatureChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: [
                            @foreach ($weatherData['hourly'] as $date => $hours)
                                '{{ \Carbon\Carbon::parse($date)->format('M d') }}',
                            @endforeach
                        ],
                        datasets: [{
                            label: 'Temperature (°C)',
                            data: [
                                @foreach ($weatherData['hourly'] as $date => $hours)
                                    {{ $hours[0]['temperature'] ?? 'null' }},
                                @endforeach
                            ],
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
                            y: {
                                title: {
                                    display: true,
                                    text: 'Temperature (°C)'
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