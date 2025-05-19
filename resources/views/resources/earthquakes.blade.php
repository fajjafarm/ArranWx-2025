@extends('layouts.vertical', ['title' => 'UK Earthquakes'])

@section('content')
    @include('layouts.partials.page-title', ['subtitle' => 'Resources', 'title' => 'UK Earthquakes in the Last 50 Days'])

    <div class="container">
        @if ($message)
            <div class="alert alert-info" role="alert">
                {{ $message }}
            </div>
        @else
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>Location</th>
                        <th>Magnitude</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($earthquakeData as $quake)
                        <tr class="{{ $quake['highlight'] ? 'table-warning' : '' }}">
                            <td>{{ $quake['time'] }}</td>
                            <td>{{ $quake['place'] }}</td>
                            <td>{{ $quake['magnitude'] }}</td>
                            <td><a href="{{ $quake['link'] }}" target="_blank">View on BGS</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        <p class="text-muted mt-3">{{ $copyright }}</p>
    </div>
@endsection