@extends('layouts.vertical', ['title' => $vesselName])

@section('content')
    @include('layouts.partials.page-title', ['subtitle' => 'Resources', 'title' => $vesselName])

    <div class="container">
        <p>Track the real-time location of {{ $vesselName }} using VesselFinder's AIS map.</p>
        <div id="vesselfinder-map">
            <script type="text/javascript">
                // Map appearance
                var width = "{{ $mapParams['width'] }}";
                var height = "{{ $mapParams['height'] }}";
                var latitude = {{ $mapParams['latitude'] }};
                var longitude = {{ $mapParams['longitude'] }};
                var zoom = {{ $mapParams['zoom'] }};
                var mmsi = "{{ $mapParams['mmsi'] }}";
                var names = {{ $mapParams['names'] ? 'true' : 'false' }};
            </script>
            <script type="text/javascript" src="https://www.vesselfinder.com/aismap.js"></script>
        </div>
        <p class="mt-3">
            <a href="{{ route('resources.ship-ais') }}" class="btn btn-primary btn-sm">Back to All Ships</a>
        </p>
    </div>
@endsection