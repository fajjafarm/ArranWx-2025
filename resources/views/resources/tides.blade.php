@extends('layouts.vertical', ['title' => 'Tide Charts'])

@section('content')
    @include('layouts.partials.page-title', ['subtitle' => 'Resources', 'title' => 'Tide Charts for Isle of Arran'])

    <div class="container">
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