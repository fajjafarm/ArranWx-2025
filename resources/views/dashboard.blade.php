@extends('layouts.vertical', ['title' => 'Arran Weather Dashboard'])

@section('html-attribute')
    data-sidenav-size="full"
@endsection

@section('css')

            <!-- Iconify for icons -->
    <script src="https://code.iconify.design/iconify-icon/1.0.8/iconify-icon.min.js"></script>
    <style>
        .ferry-status-box, .weather-warning-box, .flood-warning-box {
            width: 200px;
            margin: 10px;
            display: inline-block;
            vertical-align: top;
        }
        .meteogram-box {
            width: 600px; /* Wider for chart */
            margin: 10px;
            display: inline-block;
            vertical-align: top;
        }
        .card-body {
            color: #fff;
           
        }
        .status-normal { background-color: #28a745; }
        .status-yellow { background-color: #ffc107; }
        .status-amber { background-color: #ff8c00; }
        .status-red { background-color: #dc3545; }
        .weather-no-warning { background-color: #6c757d; } /* Grey for no warnings */
        .avatar-title {
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .text-muted {
            color: #e0e0e0 !important;
        }
        .timestamp {
            font-size: 10px;
            margin-top: 5px;
            opacity: 0.8;
        }
    </style>

    @vite(['node_modules/flatpickr/dist/flatpickr.min.css'])
    <style>
        .separator {
            margin: 20px 0;
            border-top: 2px solid #ddd;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
            display: flex;
            align-items: center;
            gap: 8px;
        }
    </style>
@endsection

@section('content')
    @include('layouts.partials.page-title', ['subtitle' => 'Weather', 'title' => 'Arran Weather Dashboard'])
<!-- Ferry Status Section -->
<div class="row justify-content-center">
    <h5 class="text-muted fs-14 mt-4 mb-3 text-center">CalMac Ferry Status - {{ now()->format('d M Y') }}</h5>
    <div class="row g-3 justify-content-center">
        <!-- Brodick to Ardrossan -->
        <div class="col-md-3 ferry-status-box">
            <div class="card">
                <div class="card-body {{ $brodickArdrossanStatus['class'] }}">
                    <h5 class="text-muted fs-13 text-uppercase" title="Brodick - Ardrossan">
                        Brodick - Ardrossan
                    </h5>
                    <div class="d-flex align-items-center justify-content-center gap-2 my-2 py-1">
                        <div class="user-img fs-42 flex-shrink-0">
                            <span class="avatar-title bg-white text-dark rounded-circle fs-22">
                                <iconify-icon icon="mdi:ferry"></iconify-icon>
                            </span>
                        </div>
                        <h3 class="mb-0 fw-bold">
                            {{ $brodickArdrossanStatus['text'] }}
                        </h3>
                    </div>
                    <p class="mb-1 text-muted timestamp">
                        Last updated: {{ $brodickArdrossanStatus['updated'] }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Brodick to Troon -->
        <div class="col-md-3 ferry-status-box">
            <div class="card">
                <div class="card-body {{ $brodickTroonStatus['class'] }}">
                    <h5 class="text-muted fs-13 text-uppercase" title="Brodick - Troon">
                        Brodick - Troon
                    </h5>
                    <div class="d-flex align-items-center justify-content-center gap-2 my-2 py-1">
                        <div class="user-img fs-42 flex-shrink-0">
                            <span class="avatar-title bg-white text-dark rounded-circle fs-22">
                                <iconify-icon icon="mdi:ferry"></iconify-icon>
                            </span>
                        </div>
                        <h3 class="mb-0 fw-bold">
                            {{ $brodickTroonStatus['text'] }}
                        </h3>
                    </div>
                    <p class="mb-1 text-muted timestamp">
                        Last updated: {{ $brodickTroonStatus['updated'] }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Lochranza to Claonai g -->
        <div class="col-md-3 ferry-status-box">
            <div class="card">
                <div class="card-body {{ $lochranzaClaonaigStatus['class'] }}">
                    <h5 class="text-muted fs-13 text-uppercase" title="Lochranza - Claonaig">
                        Lochranza - Claonaig
                    </h5>
                    <div class="d-flex align-items-center justify-content-center gap-2 my-2 py-1">
                        <div class="user-img fs-42 flex-shrink-0">
                            <span class="avatar-title bg-white text-dark rounded-circle fs-22">
                                <iconify-icon icon="mdi:ferry"></iconify-icon>
                            </span>
                        </div>
                        <h3 class="mb-0 fw-bold">
                            {{ $lochranzaClaonaigStatus['text'] }}
                        </h3>
                    </div>
                    <p class="mb-1 text-muted timestamp">
                        Last updated: {{ $lochranzaClaonaigStatus['updated'] }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
                <!-- Weather and Flood Warnings Section -->
    <div class="row justify-content-center" id="warnings-section">
        <h5 class="text-muted fs-14 mt-4 mb-3 text-center">Weather & Flood Warnings - {{ now()->format('d M Y') }}</h5>

        <!-- Met Office Weather Warnings -->
        <div class="weather-warning-box">
            <div class="card">
                <div class="card-body {{ $metOfficeWarning['class'] }}">
                    <h5 class="text-muted fs-13 text-uppercase" title="Met Office Weather Warnings">Met Office Warnings</h5>
                    <div class="d-flex align-items-center justify-content-center gap-2 my-2 py-1">
                        <div class="user-img fs-42 flex-shrink-0">
                            <span class="avatar-title bg-white text-dark rounded-circle fs-22">
                                <iconify-icon icon="mdi:weather-partly-cloudy"></iconify-icon>
                            </span>
                        </div>
                        <h3 class="mb-0 fw-bold">{{ $metOfficeWarning['text'] }}</h3>
                    </div>
                    <p class="mb-1 text-muted timestamp">Last updated: {{ $metOfficeWarning['updated'] }}</p>
                </div>
            </div>
        </div>

        <!-- Meteogram -->
        <div class="meteogram-box">
            <div class="card">
                <div class="card-body" style="background-color: #f8f9fa; padding: 10px;">
                    <h5 class="text-muted fs-13 text-uppercase" title="Meteogram">Meteogram (Arran)</h5>
                    <div class="text-center">
                        <!-- Placeholder; replace with actual meteogram -->
                        <img src="https://via.placeholder.com/580x100?text=Meteogram+Placeholder" alt="Meteogram" id="meteogram-image" style="max-width: 100%;">
                    </div>
                    <p class="mb-1 text-muted timestamp">Last updated: {{ $meteogram['updated'] }}</p>
                </div>
            </div>
        </div>

        <!-- SEPA Flood Warnings -->
        <div class="flood-warning-box">
            <div class="card">
                <div class="card-body {{ $sepaFloodWarning['class'] }}">
                    <h5 class="text-muted fs-13 text-uppercase" title="SEPA Flood Warnings">SEPA Flood Warnings</h5>
                    <div class="d-flex align-items-center justify-content-center gap-2 my-2 py-1">
                        <div class="user-img fs-42 flex-shrink-0">
                            <span class="avatar-title bg-white text-dark rounded-circle fs-22">
                                <iconify-icon icon="mdi:flood"></iconify-icon>
                            </span>
                        </div>
                        <h3 class="mb-0 fw-bold">{{ $sepaFloodWarning['text'] }}</h3>
                    </div>
                    <p class="mb-1 text-muted timestamp">Last updated: {{ $sepaFloodWarning['updated'] }}</p>
                </div>
            </div>
        </div>
    </div>
   <div class="row">
    <div class="col">
        <!-- Village Locations -->
        <div class="section-title">
            <iconify-icon icon="solar:│buildings-bold-duotone" class="fs-24"></iconify-icon>
            Villages
        </div>
        <div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 text-center">
            @foreach ($locations->where('type', 'Village') as $location)
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <!-- Unit Selection Row -->
                            <div class="unit-selection mb-2 d-flex justify-content-center gap-2 flex-wrap">
                                <select class="form-select form-select-sm temp-unit" style="width: auto;">
                                    <option value="celsius" selected>°C</option>
                                    <option value="fahrenheit">°F</option>
                                </select>
                                <select class="form-select form-select-sm speed-unit" style="width: auto;">
                                    <option value="mph" selected>mph</option>
                                    <option value="kph">kph</option>
                                    <option value="knots">knots</option>
                                    <option value="ms">m/s</option>
                                </select>
                                <select class="form-select form-select-sm rain-unit" style="width: auto;">
                                    <option value="mm" selected>mm</option>
                                    <option value="inches">inches</option>
                                </select>
                                <select class="form-select form-select-sm altitude-unit" style="width: auto;">
                                    <option value="meters" selected>m</option>
                                    <option value="feet">ft</option>
                                </select>
                            </div>
                            <h5 class="text-muted fs-13 text-uppercase" title="{{ $location->name }}">
                                <a href="{{ route('location.show', $location->name) }}" class="link-reset">
                                    {{ $location->name }} (<span class="altitude" data-altitude-meters="{{ $location->altitude ?? 0 }}">{{ $location->altitude ?? 0 }}m</span>)
                                </a>
                            </h5>
                            <div class="d-flex align-items-center justify-content-center gap-2 my-2 py-1">
                                <div class="user-img fs-42 flex-shrink-0">
                                    <span class="avatar-title text-bg-primary rounded-circle fs-22">
                                        <iconify-icon icon="solar:buildings-bold-duotone"></iconify-icon>
                                    </span>
                                </div>
                                <h3 class="mb-0 fw-bold temperature" data-temp-celsius="{{ isset($weatherData[$location->name]['weather']) ? $weatherData[$location->name]['weather']['air_temperature'] : 'N/A' }}">
                                    @if (isset($weatherData[$location->name]['weather']))
                                        {{ $weatherData[$location->name]['weather']['air_temperature'] }}°C
                                    @else
                                        N/A
                                    @endif
                                </h3>
                            </div>
                            @if (isset($weatherData[$location->name]['weather']))
                                <p class="mb-1 text-muted">
                                    <span class="me-2 wind-speed" data-speed-ms="{{ $weatherData[$location->name]['weather']['wind_speed'] }}">Wind: {{ round($weatherData[$location->name]['weather']['wind_speed'] * 2.23694, 1) }} mph</span>
                                    <span>Humidity: {{ $weatherData[$location->name]['weather']['relative_humidity'] }}%</span>
                                </p>
                                <p class="mb-1 text-muted precipitation" data-precip-mm="{{ $weatherData[$location->name]['weather']['precipitation_amount'] ?? 'N/A' }}">
                                    Rain: {{ $weatherData[$location->name]['weather']['precipitation_amount'] ?? 'N/A' }} {{ $weatherData[$location->name]['weather']['precipitation_amount'] !== 'N/A' ? 'mm' : '' }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div><!-- end col -->
            @endforeach
        </div><!-- end row -->

        <!-- Separator -->
        <hr class="separator">

        <!-- Hill Locations -->
        <div class="section-title">
            <iconify-icon icon="solar:mountains-bold-duotone" class="fs-24"></iconify-icon>
            Hills
        </div>
        <div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 text-center">
            @foreach ($locations->where('type', 'Hill') as $location)
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <!-- Unit Selection Row -->
                            <div class="unit-selection mb-2 d-flex justify-content-center gap-2 flex-wrap">
                                <select class="form-select form-select-sm temp-unit" style="width: auto;">
                                    <option value="celsius" selected>°C</option>
                                    <option value="fahrenheit">°F</option>
                                </select>
                                <select class="form-select form-select-sm speed-unit" style="width: auto;">
                                    <option value="mph" selected>mph</option>
                                    <option value="kph">kph</option>
                                    <option value="knots">knots</option>
                                    <option value="ms">m/s</option>
                                </select>
                                <select class="form-select form-select-sm rain-unit" style="width: auto;">
                                    <option value="mm" selected>mm</option>
                                    <option value="inches">inches</option>
                                </select>
                                <select class="form-select form-select-sm altitude-unit" style="width: auto;">
                                    <option value="meters" selected>m</option>
                                    <option value="feet">ft</option>
                                </select>
                            </div>
                            <h5 class="text-muted fs-13 text-uppercase" title="{{ $location->name }}">
                                <a href="{{ route('location.show', $location->name) }}" class="link-reset">
                                    {{ $location->name }} (<span class="altitude" data-altitude-meters="{{ $location->altitude ?? 0 }}">{{ $location->altitude ?? 0 }}m</span>)
                                </a>
                            </h5>
                            <div class="d-flex align-items-center justify-content-center gap-2 my-2 py-1">
                                <div class="user-img fs-42 flex-shrink-0">
                                    <span class="avatar-title text-bg-success rounded-circle fs-22">
                                        <iconify-icon icon="solar:mountains-bold-duotone"></iconify-icon>
                                    </span>
                                </div>
                                <h3 class="mb-0 fw-bold temperature" data-temp-celsius="{{ isset($weatherData[$location->name]['weather']) ? $weatherData[$location->name]['weather']['air_temperature'] : 'N/A' }}">
                                    @if (isset($weatherData[$location->name]['weather']))
                                        {{ $weatherData[$location->name]['weather']['air_temperature'] }}°C
                                    @else
                                        N/A
                                    @endif
                                </h3>
                            </div>
                            @if (isset($weatherData[$location->name]['weather']))
                                <p class="mb-1 text-muted">
                                    <span class="me-2 wind-speed" data-speed-ms="{{ $weatherData[$location->name]['weather']['wind_speed'] }}">Wind: {{ round($weatherData[$location->name]['weather']['wind_speed'] * 2.23694, 1) }} mph</span>
                                    <span>Humidity: {{ $weatherData[$location->name]['weather']['relative_humidity'] }}%</span>
                                </p>
                                <p class="mb-1 text-muted precipitation" data-precip-mm="{{ $weatherData[$location->name]['weather']['precipitation_amount'] ?? 'N/A' }}">
                                    Rain: {{ $weatherData[$location->name]['weather']['precipitation_amount'] ?? 'N/A' }} {{ $weatherData[$location->name]['weather']['precipitation_amount'] !== 'N/A' ? 'mm' : '' }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div><!-- end col -->
            @endforeach
        </div><!-- end row -->

        <!-- Separator -->
        <hr class="separator">

        <!-- Marine Locations -->
        <div class="section-title">
            <iconify-icon icon="solar:water-bold-duotone" class="fs-24"></iconify-icon>
            Marine Areas
        </div>
        <div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 text-center">
            @foreach ($locations->where('type', 'Marine') as $location)
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <!-- Unit Selection Row -->
                            <div class="unit-selection mb-2 d-flex justify-content-center gap-2 flex-wrap">
                                <select class="form-select form-select-sm temp-unit" style="width: auto;">
                                    <option value="celsius" selected>°C</option>
                                    <option value="fahrenheit">°F</option>
                                </select>
                                <select class="form-select form-select-sm speed-unit" style="width: auto;">
                                    <option value="mph" selected>mph</option>
                                    <option value="kph">kph</option>
                                    <option value="knots">knots</option>
                                    <option value="ms">m/s</option>
                                </select>
                                <select class="form-select form-select-sm rain-unit" style="width: auto;">
                                    <option value="mm" selected>mm</option>
                                    <option value="inches">inches</option>
                                </select>
                                <select class="form-select form-select-sm altitude-unit" style="width: auto;">
                                    <option value="meters" selected>m</option>
                                    <option value="feet">ft</option>
                                </select>
                            </div>
                            <h5 class="text-muted fs-13 text-uppercase" title="{{ $location->name }}">
                                <a href="{{ route('location.show', $location->name) }}" class="link-reset">
                                    {{ $location->name }} (<span class="altitude" data-altitude-meters="{{ $location->altitude ?? 0 }}">{{ $location->altitude ?? 0 }}m</span>)
                                </a>
                            </h5>
                            <div class="d-flex align-items-center justify-content-center gap-2 my-2 py-1">
                                <div class="user-img fs-42 flex-shrink-0">
                                    <span class="avatar-title text-bg-info rounded-circle fs-22">
                                        <iconify-icon icon="solar:water-bold-duotone"></iconify-icon>
                                    </span>
                                </div>
                                <h3 class="mb-0 fw-bold temperature" data-temp-celsius="{{ isset($weatherData[$location->name]['weather']) ? $weatherData[$location->name]['weather']['air_temperature'] : 'N/A' }}">
                                    @if (isset($weatherData[$location->name]['weather']))
                                        {{ $weatherData[$location->name]['weather']['air_temperature'] }}°C
                                    @else
                                        N/A
                                    @endif
                                </h3>
                            </div>
                            @if (isset($weatherData[$location->name]['weather']))
                                <p class="mb-1 text-muted">
                                    <span class="me-2 wind-speed" data-speed-ms="{{ $weatherData[$location->name]['weather']['wind_speed'] }}">Wind: {{ round($weatherData[$location->name]['weather']['wind_speed'] * 2.23694, 1) }} mph</span>
                                    <span>Humidity: {{ $weatherData[$location->name]['weather']['relative_humidity'] }}%</span>
                                </p>
                                <p class="mb-1 text-muted precipitation" data-precip-mm="{{ $weatherData[$location->name]['weather']['precipitation_amount'] ?? 'N/A' }}">
                                    Rain: {{ $weatherData[$location->name]['weather']['precipitation_amount'] ?? 'N/A' }} {{ $weatherData[$location->name]['weather']['precipitation_amount'] !== 'N/A' ? 'mm' : '' }}
                                </p>
                            @endif
                            @if (isset($weatherData[$location->name]['marine']))
                                <hr class="my-2">
                                <p class="mb-1 text-muted fs-12 sea-temperature" data-temp-celsius="{{ $weatherData[$location->name]['marine']['water_temperature'] ?? 'N/A' }}">
                                    <span class="fw-semibold">Sea Temp:</span> {{ $weatherData[$location->name]['marine']['water_temperature'] ?? 'N/A' }} {{ $weatherData[$location->name]['marine']['water_temperature'] !== 'N/A' ? '°C' : '' }}
                                </p>
                                <p class="mb-1 text-muted fs-12">
                                    <span class="fw-semibold">Wave Height:</span> {{ $weatherData[$location->name]['marine']['wave_height'] ?? 'N/A' }} m
                                </p>
                                <p class="mb-1 text-muted fs-12">
                                    <span class="fw-semibold">Swell Height:</span> {{ $weatherData[$location->name]['marine']['swell_wave_height'] ?? 'N/A' }} m
                                </p>
                            @endif
                        </div>
                    </div>
                </div><!-- end col -->
            @endforeach
        </div><!-- end row -->
    </div><!-- end col -->
</div><!-- end row -->