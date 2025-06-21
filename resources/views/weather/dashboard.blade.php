@extends('layouts.vertical', ['title' => 'Isle of Arran Weather Dashboard'])

@section('html-attribute')
    data-sidenav-size="full"
@endsection

@section('css')
    @vite(['node_modules/flatpickr/dist/flatpickr.min.css'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/weather-icons/2.0.10/css/weather-icons.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
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
        #map {
            height: 500px;
            width: 100%;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .weather-summary {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .weather-summary h5 {
            font-size: 16px;
            margin-bottom: 10px;
        }
        .weather-summary ul {
            list-style: none;
            padding: 0;
        }
        .weather-summary li {
            padding: 8px 0;
            border-bottom: 1px solid #ddd;
        }
        .weather-summary li:last-child {
            border-bottom: none;
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
            #map {
                height: 300px;
            }
        }
    </style>
@endsection

@section('content')
    @include('layouts.partials.page-title', ['subtitle' => 'Weather Overview', 'title' => 'Isle of Arran Weather Dashboard'])

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Isle of Arran Map</h5>
                    <div id="map"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body weather-summary">
                    <h5 class="text-muted fs-14 text-uppercase">Current Weather Summary (Villages)</h5>
                    @php
                        // Filter villages and calculate metrics
                        $villages = collect($weatherData)->filter(function ($data) {
                            return $data['type'] === 'Village';
                        });

                        $warmest = $villages->sortByDesc(function ($data) {
                            return $data['weather']['air_temperature'] ?? -INF;
                        })->first();

                        $coldest = $villages->sortBy(function ($data) {
                            return $data['weather']['air_temperature'] ?? INF;
                        })->first();

                        $wettest = $villages->sortByDesc(function ($data) {
                            return $data['precipitation'] ?? 0;
                        })->first();

                        $windiest = $villages->sortByDesc(function ($data) {
                            return $data['weather']['wind_speed'] ?? 0;
                        })->first();

                        $cloudiest = $villages->sortByDesc(function ($data) {
                            return $data['weather']['cloud_area_fraction'] ?? 0;
                        })->first();
                    @endphp

                    <ul>
                        <li>
                            <strong>Warmest Village:</strong> 
                            @if ($warmest)
                                {{ array_key_first($villages->toArray()) }} ({{ number_format($warmest['weather']['air_temperature'] ?? 0, 1) }}°C)
                            @else
                                N/A
                            @endif
                        </li>
                        <li>
                            <strong>Coldest Village:</strong> 
                            @if ($coldest)
                                {{ array_key_first($villages->where('weather.air_temperature', $coldest['weather']['air_temperature'])->toArray()) }} ({{ number_format($coldest['weather']['air_temperature'] ?? 0, 1) }}°C)
                            @else
                                N/A
                            @endif
                        </li>
                        <li>
                            <strong>Wettest Village:</strong> 
                            @if ($wettest)
                                {{ array_key_first($villages->where('precipitation', $wettest['precipitation'])->toArray()) }} ({{ number_format($wettest['precipitation'] ?? 0, 1) }} mm)
                            @else
                                N/A
                            @endif
                        </li>
                        <li>
                            <strong>Windiest Village:</strong> 
                            @if ($windiest)
                                {{ array_key_first($villages->where('weather.wind_speed', $windiest['weather']['wind_speed'])->toArray()) }} ({{ number_format($windiest['weather']['wind_speed'] ?? 0, 1) }} m/s)
                            @else
                                N/A
                            @endif
                        </li>
                        <li>
                            <strong>Cloudiest Village:</strong> 
                            @if ($cloudiest)
                                {{ array_key_first($villages->where('weather.cloud_area_fraction', $cloudiest['weather']['cloud_area_fraction'])->toArray()) }} ({{ number_format($cloudiest['weather']['cloud_area_fraction'] ?? 0, 1) }}%)
                            @else
                                N/A
                            @endif
                        </li>
                    </ul>
                </div>
            </div>
            <!-- Service Status and Warnings -->
            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="text-muted fs-14 text-uppercase">Service and Warning Status</h5>
                    <ul class="list-unstyled">
                        <li><strong>Brodick-Ardrossan Ferry:</strong> <span class="{{ $brodickArdrossanStatus['class'] }}">{{ $brodickArdrossanStatus['text'] }} (Updated: {{ $brodickArdrossanStatus['updated'] }})</span></li>
                        <li><strong>Brodick-Troon Ferry:</strong> <span class="{{ $brodickTroonStatus['class'] }}">{{ $brodickTroonStatus['text'] }} (Updated: {{ $brodickTroonStatus['updated'] }})</span></li>
                        <li><strong>Lochranza-Claonaig Ferry:</strong> <span class="{{ $lochranzaClaonaigStatus['class'] }}">{{ $lochranzaClaonaigStatus['text'] }} (Updated: {{ $lochranzaClaonaigStatus['updated'] }})</span></li>
                        <li><strong>Met Office Warning:</strong> <span class="{{ $metOfficeWarning['class'] }}">{{ $metOfficeWarning['text'] }} (Updated: {{ $metOfficeWarning['updated'] }})</span></li>
                        <li><strong>SEPA Flood Warning:</strong> <span class="{{ $sepaFloodWarning['class'] }}">{{ $sepaFloodWarning['text'] }} (Updated: {{ $sepaFloodWarning['updated'] }})</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize Leaflet Map
            var map = L.map('map').setView([55.5826, -5.2023], 10); // Centered on Isle of Arran

            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 18,
            }).addTo(map);

            // Custom icons for location types
            var villageIcon = L.divIcon({
                html: '<iconify-icon icon="solar:buildings-bold-duotone" style="color: #007bff; font-size: 24px;"></iconify-icon>',
                iconSize: [24, 24],
                className: 'leaflet-div-icon'
            });

            var hillIcon = L.divIcon({
                html: '<iconify-icon icon="solar:mountains-bold-duotone" style="color: #28a745; font-size: 24px;"></iconify-icon>',
                iconSize: [24, 24],
                className: 'leaflet-div-icon'
            });

            var marineIcon = L.divIcon({
                html: '<iconify-icon icon="solar:anchor-bold-duotone" style="color: #dc3545; font-size: 24px;"></iconify-icon>',
                iconSize: [24, 24],
                className: 'leaflet-div-icon'
            });

            // Add markers for each location
            @php
                $locations = collect($locations);
            @endphp
            @foreach ($locations as $location)
                @php
                    $weather = $weatherData[$location['name']] ?? [];
                    $popupContent = "<b>{$location['name']}</b><br>Type: {$location['type']}<br>";
                    if (!empty($weather['weather'])) {
                        $popupContent .= "Temp: " . (isset($weather['weather']['air_temperature']) ? number_format($weather['weather']['air_temperature'], 1) . "°C" : "N/A") . "<br>";
                        $popupContent .= "Wind: " . (isset($weather['weather']['wind_speed']) ? number_format($weather['weather']['wind_speed'], 1) . " m/s (" . $weather['wind_direction'] . ")" : "N/A") . "<br>";
                        $popupContent .= "Precipitation: " . (isset($weather['precipitation']) ? number_format($weather['precipitation'], 1) . " mm" : "N/A") . "<br>";
                        $popupContent .= "Cloud Cover: " . (isset($weather['weather']['cloud_area_fraction']) ? number_format($weather['weather']['cloud_area_fraction'], 1) . "%" : "N/A");
                        if ($location['type'] === 'Marine' && !empty($weather['marine'])) {
                            $popupContent .= "<br>Wave Height: " . (isset($weather['marine']['wave_height']) ? number_format($weather['marine']['wave_height'], 1) . " m" : "N/A");
                        }
                    } else {
                        $popupContent .= "No weather data available";
                    }
                @endphp
                L.marker([{{ $location['latitude'] }}, {{ $location['longitude'] }}], {
                    icon: {{ $location['type'] === 'Village' ? 'villageIcon' : ($location['type'] === 'Hill' ? 'hillIcon' : 'marineIcon') }}
                }).addTo(map)
                  .bindPopup(@json($popupContent))
                  .on('click', function() { window.location.href = '{{ route('weather.show', ['name' => $location['name']]) }}'; });
            @endforeach
        });
    </script>
@endsection