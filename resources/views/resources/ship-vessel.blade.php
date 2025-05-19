@extends('layouts.vertical', ['title' => $vesselName])

@section('content')
    @include('layouts.partials.page-title', ['subtitle' => 'Resources', 'title' => $vesselName])

    <div class="container">
        <p>Track the real-time location of {{ $vesselName }} using VesselFinder's AIS map.</p>
        <iframe src="{{ $mapUrl }}" width="100%" height="600px" frameborder="0" loading="lazy"></iframe>
        <p class="mt-3">
            <a href="{{ route('resources.ship-ais') }}" class="btn btn-primary btn-sm">Back to All Ships</a>
        </p>
    </div>
@endsection