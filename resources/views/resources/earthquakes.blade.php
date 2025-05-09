@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>UK Earthquakes in the Last 50 Days</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Date & Time</th>
                    <th>Location</th>
                    <th>Magnitude</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($earthquakeData as $quake)
                    <tr class="{{ $quake['highlight'] ? 'table-warning' : '' }}">
                        <td>{{ $quake['time'] }}</td>
                        <td>{{ $quake['place'] }}</td>
                        <td>{{ $quake['magnitude'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection