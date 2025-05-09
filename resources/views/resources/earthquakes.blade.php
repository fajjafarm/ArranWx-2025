@extends('layouts.vertical', ['title' => 'UK Earthquakes'])

@section('content')
    @include('layouts.partials.page-title', ['subtitle' => 'Resources', 'title' => 'UK Earthquakes in the Last 50 Days'])

    <div class="container">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Date & Time</th>
                    <th或者
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