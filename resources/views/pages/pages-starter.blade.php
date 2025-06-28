@extends('layouts.vertical', ['title' => 'Starter Page'])

@section('content')
    @include('layouts.partials.page-title', ['subtitle' => 'Pages', 'title' => 'Starter'])
    <div class="card">
        <div class="card-body">
            <p class="text-muted">Welcome to the Arran Weather Station. Use the sidebar to navigate to weather forecasts for various locations on the Isle of Arran or explore additional resources like earthquake data, aurora forecasts, and more.</p>
            <a href="{{ route('weather.index') }}" class="btn btn-primary mt-3">View Weather Dashboard</a>
        </div>
    </div>
@endsection