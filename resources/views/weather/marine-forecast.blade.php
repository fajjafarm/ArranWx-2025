@extends('layouts.app')

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
        <!-- Daily Forecast Table -->
        <div class="w-full overflow-x-auto">
            <table class="min-w-full border-collapse">
                <thead>
                    <tr>
                        <th scope="col" class="bg-gray-200 px-4 py-2">Daily Marine Forecast</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($weatherData['daily_marine_data'] as $dailyData)
                        <?php $date = $dailyData['date']; ?>
                        <tr class="bg-gray-100 border-t">
                            <!-- Table Header with Sun/Moon Data -->
                            <th class="bg-blue-400 px-4 py-2 text-left">
                                <h3 class="font-bold">{{ \Carbon\Carbon::parse($date)->format('l, d M Y') }}</h3>
                                <p class="text-sm">
                                    Sunrise: {{ $weatherData['sun'][$date]['sunrise'] }} |
                                    Sunset: {{ $weatherData['sun'][$date]['sunset'] }} <br>
                                    Moonrise: {{ $weatherData['sun'][$date]['moonrise'] }} |
                                    Moonset: {{ $weatherData['sun'][$date]['moonset'] }}
                                </p>
                                <p class="text-sm font-medium mt-2">
                                    Max Wave Height: {{ $dailyData['wave_height_max'] }} m |
                                    Wave Direction: {{ $controller->degreesToCardinal($dailyData['wave_direction_dominant']) }}
                                </p>
                            </th>
                        </tr>
                        <!-- Hourly Marine Data -->
                        @foreach ($weatherData['marine_hourly'] as $hour)
                            @if (\Carbon\Carbon::parse($hour['time'])->toDateString() == $date)
                                <tr class="border-b">
                                    <td class="px-4 py-2">
                                        <div class="flex justify-between items-center flex-wrap">
                                            <span>{{ \Carbon\Carbon::parse($hour['time'])->format('H:i') }}</span>
                                            <span>Wave Height: {{ $hour['wave_height'] ?? 'N/A' }} m</span>
                                            <span>Sea Temp: {{ $hour['sea_surface_temperature'] ?? 'N/A' }} °C</span>
                                            <span>Wave Dir: {{ $controller->degreesToCardinal($hour['wave_direction']) ?? 'N/A' }}</span>
                                            <span>Wave Period: {{ $hour['wave_period'] ?? 'N/A' }} sec</span>
                                            <span>Wind Wave: {{ $hour['wind_wave_height'] ?? 'N/A' }} m</span>
                                            <span>Swell: {{ $hour['swell_wave_height'] ?? 'N/A' }} m</span>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection