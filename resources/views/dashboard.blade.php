@extends('layouts.vertical', ['title' => 'Arran Weather Dashboard'])

@section('html-attribute')
    data-sidenav-size="full"
@endsection

@section('css')

    <style>
        .ferry-status-box {
            width: 200px;
            height: 150px;
            margin: 10px;
            display: inline-block;
            vertical-align: top;
        }
        .ferry-status-box .card-body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100%;
            color: #fff; /* White text for contrast */
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5); /* Improve readability */
        }
        .status-normal { background-color: #28a745; }
        .status-yellow { background-color: #ffc107; }
        .status-amber { background-color: #ff8c00; }
        .status-red { background-color: #dc3545; }
    </style>
@endsection
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

        <!-- Brodick to Ardrossan -->
        <div class="ferry-status-box">
            <div class="card">
                <div class="card-body {{ $brodickArdrossanStatus['class'] }}">
                    <h6 class="mb-1">Brodick - Ardrossan</h6>
                    <p class="mb-0">{{ $brodickArdrossanStatus['text'] }}</p>
                </div>
            </div>
        </div>

        <!-- Brodick to Troon -->
        <div class="ferry-status-box">
            <div class="card">
                <div class="card-body {{ $brodickTroonStatus['class'] }}">
                    <h6 class="mb-1">Brodick - Troon</h6>
                    <p class="mb-0">{{ $brodickTroonStatus['text'] }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <!-- Village Locations -->
            <div class="section-title">
                <iconify-icon icon="solar:buildings-bold-duotone" class="fs-24"></iconify-icon>
                Villages
            </div>
            <div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 text-center">
                @foreach ($locations->where('type', 'Village') as $location)
                    <div class="col">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="text-muted fs-13 text-uppercase" title="{{ $location->name }}">
                                    <a href="{{ route('location.show', $location->name) }}" class="link-reset">
                                        {{ $location->name }} ({{ $location->altitude ?? 0 }}m)
                                    </a>
                                </h5>
                                <div class="d-flex align-items-center justify-content-center gap-2 my-2 py-1">
                                    <div class="user-img fs-42 flex-shrink-0">
                                        <span class="avatar-title text-bg-primary rounded-circle fs-22">
                                            <iconify-icon icon="solar:buildings-bold-duotone"></iconify-icon>
                                        </span>
                                    </div>
                                    <h3 class="mb-0 fw-bold">
                                        @if (isset($weatherData[$location->name]['weather']))
                                            {{ $weatherData[$location->name]['weather']['air_temperature'] }}°C
                                        @else
                                            N/A
                                        @endif
                                    </h3>
                                </div>
                                @if (isset($weatherData[$location->name]['weather']))
                                    <p class="mb-1 text-muted">
                                        <span class="me-2">Wind: {{ $weatherData[$location->name]['weather']['wind_speed'] }} m/s</span>
                                        <span>Humidity: {{ $weatherData[$location->name]['weather']['relative_humidity'] }}%</span>
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
                                <h5 class="text-muted fs-13 text-uppercase" title="{{ $location->name }}">
                                    <a href="{{ route('location.show', $location->name) }}" class="link-reset">
                                        {{ $location->name }} ({{ $location->altitude ?? 0 }}m)
                                    </a>
                                </h5>
                                <div class="d-flex align-items-center justify-content-center gap-2 my-2 py-1">
                                    <div class="user-img fs-42 flex-shrink-0">
                                        <span class="avatar-title text-bg-success rounded-circle fs-22">
                                            <iconify-icon icon="solar:mountains-bold-duotone"></iconify-icon>
                                        </span>
                                    </div>
                                    <h3 class="mb-0 fw-bold">
                                        @if (isset($weatherData[$location->name]['weather']))
                                            {{ $weatherData[$location->name]['weather']['air_temperature'] }}°C
                                        @else
                                            N/A
                                        @endif
                                    </h3>
                                </div>
                                @if (isset($weatherData[$location->name]['weather']))
                                    <p class="mb-1 text-muted">
                                        <span class="me-2">Wind: {{ $weatherData[$location->name]['weather']['wind_speed'] }} m/s</span>
                                        <span>Humidity: {{ $weatherData[$location->name]['weather']['relative_humidity'] }}%</span>
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
                                <h5 class="text-muted fs-13 text-uppercase" title="{{ $location->name }}">
                                    <a href="{{ route('location.show', $location->name) }}" class="link-reset">
                                        {{ $location->name }} ({{ $location->altitude ?? 0 }}m)
                                    </a>
                                </h5>
                                <div class="d-flex align-items-center justify-content-center gap-2 my-2 py-1">
                                    <div class="user-img fs-42 flex-shrink-0">
                                        <span class="avatar-title text-bg-info rounded-circle fs-22">
                                            <iconify-icon icon="solar:water-bold-duotone"></iconify-icon>
                                        </span>
                                    </div>
                                    <h3 class="mb-0 fw-bold">
                                        @if (isset($weatherData[$location->name]['weather']))
                                            {{ $weatherData[$location->name]['weather']['air_temperature'] }}°C
                                        @else
                                            N/A
                                        @endif
                                    </h3>
                                </div>
                                @if (isset($weatherData[$location->name]['weather']))
                                    <p class="mb-1 text-muted">
                                        <span class="me-2">Wind: {{ $weatherData[$location->name]['weather']['wind_speed'] }} m/s</span>
                                        <span>Humidity: {{ $weatherData[$location->name]['weather']['relative_humidity'] }}%</span>
                                    </p>
                                @endif
                                @if (isset($weatherData[$location->name]['marine']))
                                    <hr class="my-2">
                                    <p class="mb-1 text-muted fs-12">
                                        <span class="fw-semibold">Sea Temp:</span> {{ $weatherData[$location->name]['marine']['water_temperature'] ?? 'N/A' }}°C
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
@endsection

@section('scripts')
    @vite(['resources/js/pages/dashboard-sales.js'])
@endsection