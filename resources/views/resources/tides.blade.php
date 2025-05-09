@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Tide Charts for Isle of Arran</h1>
        @foreach ($locations as $location)
            <h2>{{ $location }}</h2>
            <p>Date: {{ $tideData[$location]['date'] }}</p>
            <h3>High Tides</h3>
            <ul>
                @foreach ($tideData[$location]['high_tides'] as $tide)
                    <li>{{ $tide['time'] }} - {{ $tide['height'] }}</li>
                @endforeach
            </ul>
            <h3>Low Tides</h3>
            <ul>
                @foreach ($tideData[$location]['low_tides'] as $tide)
                    <li>{{ $tide['time'] }} - {{ $tide['height'] }}</li>
                @endforeach
            </ul>
        @endforeach
    </div>
@endsection