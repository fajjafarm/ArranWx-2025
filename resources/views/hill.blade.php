@extends('layouts.vertical', ['title' => $location->name . ' Weather'])

@section('html-attribute')
    data-sidenav-size="full"
@endsection

@section('css')
    @vite(['node_modules/flatpickr/dist/flatpickr.min.css'])
    <style>
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
                            <span class="avatar-title text-bg-success rounded-circle fs-22">
                                <iconify-icon icon="solar:mountains-bold-duotone"></iconify-icon>
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
                            <span class="me-2">Gust: {{ $weatherData['hourly'][array_key_first($weatherData['hourly'])]['wind_gust'] ?? 'N/A' }} m/s</span>
                            <span>Humidity: {{ $weatherData['current']['relative_humidity'] ?? 'N/A' }}%</span>
                        </p>
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
                                    <th>Gust (m/s)</th>
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
                                            <td>{{ $hour['wind_gust'] ?? 'N/A' }}</td>
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
                        Debug: Sun/Moon = {{ json_encode($weatherData['sun'], JSON_PRETTY_PRINT) }}
                    </pre>

                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-light mt-3">Back to Dashboard</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @vite(['resources/js/pages/dashboard-sales.js'])
@endsection