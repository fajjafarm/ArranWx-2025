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
                <h1 class="text-3xl font-bold">{{ $location->name }} Weather Forecast</h1>
                <p class="text-gray-600 mt-2">
                    {{ $location->description ?? 'Weather forecast for ' . $location->name . ', located at latitude ' . $location->latitude . ' meters above sea level.' }}
                </p>
            </div>
        <!-- Daily Forecast Table -->
        </div>
        <div class="w-full overflow-x-auto">
            <table class="min-w-full border-collapse">
                <thead>
                    <tr>
                        <th scope="col" class="bg-gray-200 px-4 py-2">Daily Forecast</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($weatherData['hourly'] as $date => $hours)
                        <tr class="bg-gray-100 border-t">
                            <!-- Table Header with Sun/Moon Data -->
                            <th class="bg-blue-100 px-4 py-2 text-left">
                                <h3 class="font-bold">{{ \Carbon\Carbon::parse($date)->format('l, j M Y') }}</h3>
                                <p class="text-sm">
                                    Sunrise: {{ $weatherData['sun'][$date]['sunrise'] }} |
                                    Sunset: {{ $weatherData['sun'][$date]['sunset'] }} <br>
                                    Moonrise: {{ $weatherData['sun'][$date]['moonrise'] }} |
                                    Moonset: {{ $weatherData['sun'][$date]['moonset'] }}
                                </p>
                            </th>
                        </tr>
                        <!-- Hourly Data -->
                        @foreach ($hours as $hour)
                            <tr class="border-b">
                                <td class="px-4 py-2">
                                    <div class="flex justify-between items-center">
                                        <span>{{ $hour['time'] }}</span>
                                        <span>{{ $hour['temperature'] ?? 'N/A' }}°C</span>
                                        <span>{{ $hour['condition'] }}</span>
                                        <span>Wind: {{ $hour['wind_speed'] }} mph {{ $hour['wind_direction'] }} (Gust {{ $hour['wind_gust'] }} mph)</span>
                                        <span>Precip: {{ $hour['precipitation'] }} mm</span>
                                        <span>Pressure: {{ $hour['pressure'] ?? 'N/A' }} hPa</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection