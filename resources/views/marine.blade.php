@extends('layouts.app', ['title' => $location->name . ' Marine Forecast'])

@section('html-content')
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
        }
        .wave-bar {
            width: 20px;
            background-color: #3498db;
            transition: height 0.3s;
        }
        .wave-label {
            display: block;
            text-align: center;
            font-size: 12px;
            color: #333;
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
            margin-top: 10px;
        }
        .forecast-table th,
        .forecast-table td {
            padding: 8px;
            text-align: center;
            border-bottom: 1px solid #dee2e6;
        }
        .forecast-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .daily-info {
            background-color: #e9ecef;
            font-size: 12px;
        }
        .accordion-button {
            font-size: 14px;
        }
        @media (max-width: 767px) {
            .forecast-table th,
            .forecast-table td {
                padding: 6px;
                font-size: 12px;
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
    <div class="container mx-auto px-4 py-8">
        <!-- Header Section: Map, Title, Description -->
        <div class="flex flex-col md:flex-row items-start mb-8">
            <!-- Map -->
            <div class="w-32 h-32 md:w-48 md:h-48 flex-shrink-0">
                <img src="https://api.mapbox.com/styles/v1/mapbox/streets-v11/static/pin-s+ff0000({{ $location->longitude }},{{ $location->latitude }})/{{ $location->longitude }},{{ $location->latitude }},12,0/150x150?access_token={{ env('MAPBOX_ACCESS_TOKEN') }}"
                     alt="Map of {{ $location->name }}"
                     class="w-full h-full object-cover rounded">
            </div>
            <!-- Title and Description -->
            <div class="md:ml-6 mt-4 md:mt-0">
                <h1 class="text-3xl font-bold">{{ $location->name }} Marine Forecast</h1>
                <p class="text-gray-600 mt-2">
                    {{ $location->description ?? 'Marine forecast for ' . $location->name . ' at sea.' }}
                </p>
            </div>
        </div>

        <!-- Current Conditions -->
        <div class="bg-white shadow rounded-lg p-6 mb-8">
            <h4 class="text-gray-500 text-sm uppercase text-center mb-4">
                {{ $location->name }} - Current Conditions
            </h4>
            <div class="flex items-center justify-center gap-4 mb-4">
                <span class="bg-blue-100 text-blue-600 rounded-full p-3">
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </span>
                <h3 class="text-2xl font-bold">
                    @if (isset($weatherData['current']['air_temperature']))
                        {{ number_format($weatherData['current']['air_temperature'], 1) }}°C
                    @else
                        N/A
                    @endif
                </h3>
            </div>
            @if (isset($weatherData['current']))
                <p class="text-center text-gray-600 mb-4">
                    <span class="mr-4">Wind: {{ $weatherData['current']['wind_speed'] ?? 'N/A' }} m/s</span>
                    <span>Humidity: {{ $weatherData['current']['relative_humidity'] ?? 'N/A' }}%</span>
                </p>
            @endif

            <!-- Marine Current Conditions -->
            @if (isset($weatherData['marine']))
                <hr class="my-4">
                <h6 class="text-gray-500 text-center mb-4">Marine Weather Conditions</h6>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm text-gray-600">
                    <div>
                        <span class="font-semibold">Sea Temp:</span> {{ $weatherData['marine']['sea_surface_temperature'] ?? 'N/A' }}°C
                    </div>
                    <div>
                        <span class="font-semibold">Wave Height:</span> {{ $weatherData['marine']['wave_height'] ?? 'N/A' }} m
                    </div>
                    <div>
                        <span class="font-semibold">Wave Dir:</span> {{ $weatherData['marine']['wave_direction'] ? $controller->degreesToCardinal($weatherData['marine']['wave_direction']) : 'N/A' }}
                    </div>
                    <div>
                        <span class="font-semibold">Wave Period:</span> {{ $weatherData['marine']['wave_period'] ?? 'N/A' }} s
                    </div>
                    <div>
                        <span class="font-semibold">Wind Wave:</span> {{ $weatherData['marine']['wind_wave_height'] ?? 'N/A' }} m
                    </div>
                    <div>
                        <span class="font-semibold">Swell Wave:</span> {{ $weatherData['marine']['swell_wave_height'] ?? 'N/A' }} m
                    </div>
                    <div>
                        <span class="font-semibold">Sea Level:</span> {{ $weatherData['marine']['sea_level_height_msl'] ?? 'N/A' }} m
                    </div>
                </div>
                <!-- Wave Graphic Bars -->
                <div class="wave-graphic justify-center">
                    <div>
                        <div class="wave-bar" style="height: {{ ($weatherData['marine']['wave_height'] ?? 0) * 20 }}px;"></div>
                        <span class="wave-label">Wave</span>
                    </div>
                    <div>
                        <div class="wave-bar" style="height: {{ ($weatherData['marine']['wind_wave_height'] ?? 0) * 20 }}px;"></div>
                        <span class="wave-label">Wind Wave</span>
                    </div>
                    <div>
                        <div class="wave-bar" style="height: {{ ($weatherData['marine']['swell_wave_height'] ?? 0) * 20 }}px;"></div>
                        <span class="wave-label">Swell</span>
                    </div>
                </div>
                <!-- Wave Direction Arrow -->
                @if (isset($weatherData['marine']['wave_direction']))
                    <div class="wave-direction">
                        <div class="wave-arrow" style="transform: translate(-50%, -50%) rotate({{ $weatherData['marine']['wave_direction'] }}deg);"></div>
                    </div>
                @endif
            @endif
        </div>

        <!-- 7-Day Marine Forecast Chart -->
        @if (!empty($weatherData['marine_forecast']))
            <div class="bg-white shadow rounded-lg p-6 mb-8">
                <h6 class="text-gray-500 text-center mb-4">7-Day Marine Forecast - Maximum Wave Height</h6>
                <canvas id="marineWaveHeightChart" style="max-height: 200px; margin: 20px auto;"></canvas>
            </div>
        @endif

        <!-- Hourly Marine Forecast Accordion -->
        @if (!empty($weatherData['marine_hourly']))
            <div class="bg-white shadow rounded-lg p-6">
                <h6 class="text-gray-500 text-center mb-4">Hourly Marine Forecast</h6>
                <div class="accordion" id="marineForecastAccordion">
                    @php
                        // Group hourly data by day
                        $hourlyByDay = [];
                        foreach ($weatherData['marine_hourly'] as $hour) {
                            $date = \Carbon\Carbon::parse($hour['time'])->format('Y-m-d');
                            $hourlyByDay[$date][] = $hour;
                        }
                    @endphp
                    @foreach ($hourlyByDay as $date => $hours)
                        @php
                            $dailyData = collect($weatherData['daily_marine_data'])->firstWhere('date', $date);
                        @endphp
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ $date }}">
                                <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $date }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="collapse{{ $date }}">
                                    {{ \Carbon\Carbon::parse($date)->format('l, d M Y') }}
                                </button>
                            </h2>
                            <div id="collapse{{ $date }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" aria-labelledby="heading{{ $date }}" data-bs-parent="#marineForecastAccordion">
                                <div class="accordion-body">
                                    <!-- Daily Summary with Sun/Moon Data -->
                                    <div class="daily-info p-2 mb-4">
                                        <p class="text-sm">
                                            <strong>Daily Summary:</strong>
                                            Max Wave Height: {{ $dailyData['wave_height_max'] ?? 'N/A' }} m,
                                            Max Wind Wave Height: {{ $dailyData['wind_wave_height_max'] ?? 'N/A' }} m,
                                            Max Swell Height: {{ $dailyData['swell_wave_height_max'] ?? 'N/A' }} m,
                                            Dominant Wave Dir: {{ $dailyData['wave_direction_dominant'] ? $controller->degreesToCardinal($dailyData['wave_direction_dominant']) : 'N/A' }},
                                            Dominant Wind Wave Dir: {{ $dailyData['wind_wave_direction_dominant'] ? $controller->degreesToCardinal($dailyData['wind_wave_direction_dominant']) : 'N/A' }}
                                        </p>
                                        <p class="text-sm">
                                            Sunrise: {{ $weatherData['sun'][$date]['sunrise'] ?? 'N/A' }} |
                                            Sunset: {{ $weatherData['sun'][$date]['sunset'] ?? 'N/A' }} |
                                            Moonrise: {{ $weatherData['sun'][$date]['moonrise'] ?? 'N/A' }} |
                                            Moonset: {{ $weatherData['sun'][$date]['moonset'] ?? 'N/A' }}
                                        </p>
                                    </div>
                                    <!-- Hourly Forecast Table -->
                                    <table class="forecast-table">
                                        <thead>
                                            <tr>
                                                <th>Time (BST)</th>
                                                <th>Wave Height (m)</th>
                                                <th>Swell Height (m)</th>
                                                <th>Sea Temp (°C)</th>
                                                <th>Sea Level (m)</th>
                                                <th>Wave Dir</th>
                                                <th>Wave Period (s)</th>
                                                <th>Wind Wave Height (m)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($hours as $hour)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($hour['time'])->setTimezone('Europe/London')->format('H:i') }}</td>
                                                    <td>{{ number_format($hour['wave_height'], 2) }}</td>
                                                    <td>{{ number_format($hour['swell_wave_height'], 2) }}</td>
                                                    <td>{{ number_format($hour['sea_surface_temperature'], 1) }}</td>
                                                    <td>{{ number_format($hour['sea_level_height_msl'], 2) }}</td>
                                                    <td>{{ $hour['wave_direction'] ? $controller->degreesToCardinal($hour['wave_direction']) : 'N/A' }}</td>
                                                    <td>{{ number_format($hour['wave_period'], 1) }}</td>
                                                    <td>{{ number_format($hour['wind_wave_height'], 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Back to Dashboard -->
        <div class="text-center mt-6">
            <a href="{{ route('dashboard') }}" class="inline-block bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">Back to Dashboard</a>
        </div>

        <!-- Debug Output (Optional) -->
        @if (config('app.debug'))
            <pre class="text-left text-xs bg-gray-100 p-4 mt-8 rounded">
                Debug: Marine API URL = {{ $weatherData['marine_api_url'] ?? 'N/A' }}
                Debug: Current Weather = {{ json_encode($weatherData['current'], JSON_PRETTY_PRINT) }}
                Debug: Marine Current = {{ json_encode($weatherData['marine'], JSON_PRETTY_PRINT) }}
                Debug: Marine Forecast = {{ json_encode($weatherData['marine_forecast'], JSON_PRETTY_PRINT) }}
                Debug: Daily Marine Data = {{ json_encode($weatherData['daily_marine_data'], JSON_PRETTY_PRINT) }}
            </pre>
        @endif
    </div>
@endsection

@section('scripts')
    @vite(['resources/js/pages/dashboard-sales.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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