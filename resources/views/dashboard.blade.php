@extends('layouts.vertical', ['title' => 'Arran Weather Dashboard'])

@section('html-attribute')
    data-sidenav-size="full"
@endsection

@section('css')
    @vite(['node_modules/flatpickr/dist/flatpickr.min.css'])
@endsection

@section('content')
    @include('layouts.partials.page-title', ['subtitle' => 'Weather', 'title' => 'Arran Weather Dashboard'])

    <div class="row">
        <div class="col">
            <div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 text-center">
                @foreach ($locations as $location)
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
                                        <span class="avatar-title {{ $weatherData[$location->name]['type'] === 'Hill' ? 'text-bg-success' : ($weatherData[$location->name]['type'] === 'Marine' ? 'text-bg-info' : 'text-bg-primary') }} rounded-circle fs-22">
                                            <iconify-icon icon="{{ $weatherData[$location->name]['type'] === 'Hill' ? 'solar:mountains-bold-duotone' : ($weatherData[$location->name]['type'] === 'Marine' ? 'solar:water-bold-duotone' : 'solar:buildings-bold-duotone') }}"></iconify-icon>
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
                                @if ($weatherData[$location->name]['type'] === 'Marine' && isset($weatherData[$location->name]['marine']))
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