@extends('layouts.vertical', ['title' => $location->name . ' Weather'])

@section('html-attribute')
    data-sidenav-size="full"
@endsection

@section('css')
    @vite(['node_modules/flatpickr/dist/flatpickr.min.css'])
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
            transform-origin: center bottom;
        }
        .forecast-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .forecast-table th, .forecast-table td {
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        .forecast-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        @media (max-width: 768px) {
            .forecast-table th, .forecast-table td {
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
                            @if (isset($weatherData['current']))
                                {{ $weatherData['current']['air_temperature'] }}°C
                            @else
                                N/A
                            @endif
                        </h3>
                    </div>
                    @if (isset($weatherData['current']))
                        <p class="mb-1 text-muted">
                            <span class="me-2">Wind: {{ $weatherData['current']['wind_speed'] }} m/s</span>
                            <span>Humidity: {{ $weatherData['current']['relative_humidity'] }}%</span>
                        </p>
                    @endif
                    @if ($weatherData['type'] === 'Marine' && isset($weatherData['marine']))
                        <hr class="my-2">
                        <h6 class="text-muted fs-14">Current Marine Conditions</h6>
                        <p class="mb-1 text-muted fs-12">
                            <span class="fw-semibold">Sea Temp:</span> {{ $weatherData['marine']['water_temperature'] ?? 'N/A' }}°C
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

                    <!-- 10-Day Forecast Table -->
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
                            @foreach ($weatherData['forecast'] as $day)
                                <?php $dateOnly = \Carbon\Carbon::parse($day['date'])->toDateString(); ?>
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($day['date'])->format('M d') }}</td>
                                    <td>{{ $day['temperature'] ?? 'N/A' }}</td>
                                    <td>{{ $day['wind_speed'] ?? 'N/A' }}</td>
                                    <td>{{ $day['humidity'] ?? 'N/A' }}</td>
                                    <td>{{ $weatherData['sun'][$dateOnly]['sunrise'] ? \Carbon\Carbon::parse($weatherData['sun'][$dateOnly]['sunrise'])->format('H:i') : 'N/A' }}</td>
                                    <td>{{ $weatherData['sun'][$dateOnly]['sunset'] ? \Carbon\Carbon::parse($weatherData['sun'][$dateOnly]['sunset'])->format('H:i') : 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-light mt-3">Back to Dashboard</a>
                </div>
            </div>
        </div><!-- end col -->
    </div><!-- end row -->
@endsection

@section('scripts')
    @vite(['resources/js/pages/dashboard-sales.js'])
@endsection