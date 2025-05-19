@extends('layouts.vertical', ['title' => 'Ship AIS Map'])

@section('content')
    @include('layouts.partials.page-title', ['subtitle' => 'Resources', 'title' => 'Ships Near Arran'])

    <div class="container">
        <p>View real-time ship movements around the Isle of Arran using VesselFinder's AIS map. Track specific vessels below.</p>
        <div id="vesselfinder-map">
            <script type="text/javascript">
                // Map appearance
                var width = "{{ $mapParams['width'] }}";
                var height = "{{ $mapParams['height'] }}";
                var latitude = {{ $mapParams['latitude'] }};
                var longitude = {{ $mapParams['longitude'] }};
                var zoom = {{ $mapParams['zoom'] }};
                var names = {{ $mapParams['names'] ? 'true' : 'false' }};
            </script>
            <script type="text/javascript" src="https://www.vesselfinder.com/aismap.js"></script>
        </div>
        <h3 class="mt-4">Track Specific Vessels</h3>
        <ul class="list-group">
            @foreach ($vesselLinks as $vessel)
                <li class="list-group-item">
                    <a href="{{ route($vessel['route']) }}">{{ $vessel['name'] }}</a>
                </li>
            @endforeach
        </ul>
    </div>
@endsection