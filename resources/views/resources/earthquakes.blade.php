@extends('layouts.vertical', ['title' => 'UK Earthquakes'])

@section('content')
    @include('layouts.partials.page-title', ['subtitle' => 'Resources', 'title' => 'UK Earthquakes in the Last 60 Days'])

    <div class="container">
        @if ($message)
            <div class="alert alert-info" role="alert">
                {{ $message }}
            </div>
        @endif
        @if (!empty($earthquakeData))
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
                            <td>{{ number_format($quake['magnitude'], 1) }}</td>
                            <td><a href="{{ $quake['link'] }}" target="_blank">View on BGS</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <!-- Debug output for empty data -->
            <p class="text-muted">No earthquake data available in the database for the last 60 days.</p>
        @endif
        <p class="text-muted mt-3">{{ $copyright }}</p>
    </div>
@endsection