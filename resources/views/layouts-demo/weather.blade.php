@extends('layouts.vertical', ['title' => 'Full Layout '])

@section('html-attribute')
    data-sidenav-size="full"
@endsection

@section('css')
    @vite(['node_modules/flatpickr/dist/flatpickr.min.css'])
@endsection

@section('content')
    @include('layouts.partials.page-title', ['subtitle' => 'Layout', 'title' => 'Full View'])

    <h2 class="mb-4">Arran Weather Dashboard</h2>
    <div class="row">
        @foreach ($locations as $location)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header {{ $weatherData[$location->name]['type'] === 'Hill' ? 'bg-success' : ($weatherData[$location->name]['type'] === 'Marine' ? 'bg-info' : 'bg-primary') }} text-white">
                        <h5 class="card-title mb-0">{{ $location->name }} ({{ $weatherData[$location->name]['altitude'] }}m) - {{ $weatherData[$location->name]['type'] }}</h5>
                    </div>
                    <div class="card-body">
                        @if (isset($weatherData[$location->name]['weather']))
                            <p><i class="feather icon-thermometer"></i> Temp: {{ $weatherData[$location->name]['weather']['air_temperature'] }}°C</p>
                            <p><i class="feather icon-wind"></i> Wind: {{ $weatherData[$location->name]['weather']['wind_speed'] }} m/s</p>
                            <p><i class="feather icon-droplet"></i> Humidity: {{ $weatherData[$location->name]['weather']['relative_humidity'] }}%</p>
                        @endif
                        @if ($weatherData[$location->name]['type'] === 'Marine' && isset($weatherData[$location->name]['marine']))
                            <hr>
                            <h6>Marine Forecast</h6>
                            <p>Sea Temp: {{ $weatherData[$location->name]['marine']['water_temperature'] ?? 'N/A' }}°C</p>
                            <p>Wave Height: {{ $weatherData[$location->name]['marine']['wave_height'] ?? 'N/A' }} m</p>
                            <p>Wave Direction: {{ $weatherData[$location->name]['marine']['wave_direction'] ?? 'N/A' }}°</p>
                            <p>Wave Period: {{ $weatherData[$location->name]['marine']['wave_period'] ?? 'N/A' }} s</p>
                            <p>Swell Height: {{ $weatherData[$location->name]['marine']['swell_wave_height'] ?? 'N/A' }} m</p>
                            <p>Swell Direction: {{ $weatherData[$location->name]['marine']['swell_wave_direction'] ?? 'N/A' }}°</p>
                            <p>Swell Period: {{ $weatherData[$location->name]['marine']['swell_wave_period'] ?? 'N/A' }} s</p>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection

@section('scripts')
    @vite(['resources/js/pages/dashboard-sales.js'])
@endsection
