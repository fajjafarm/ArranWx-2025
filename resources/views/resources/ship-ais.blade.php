@extends('layouts.vertical', ['title' => 'Ship AIS Map'])

@section('content')
    @include('layouts.partials.page-title', ['subtitle' => 'Resources', 'title' => 'Ship AIS Map'])

    <div class="container">
        <h4>Ships Near Arran</h4>
        @if (isset($error))
            <div class="alert alert-danger" role="alert">{{ $error }}</div>
        @endif
        @if (!empty($mapParams))
            <div id="vesselfinder-map" style="width: {{ $mapParams['width'] }}; height: {{ $mapParams['height'] }}px;"></div>
            <script type="text/javascript">
                var width = "{{ $mapParams['width'] }}";
                var height = "{{ $mapParams['height'] }}";
                var latitude = {{ $mapParams['latitude'] }};
                var longitude = {{ $mapParams['longitude'] }};
                var zoom = {{ $mapParams['zoom'] }};
                var names = {{ $mapParams['names'] ? 'true' : 'false' }};
            </script>
            <script type="text/javascript" src="https://www.vesselfinder.com/aismap.js"></script>
        @else
            <p class="text-muted">Map unavailable.</p>
        @endif

        @if (!empty($vesselLinks))
            <h5>Tracked Vessels</h5>
            <ul class="list-group mt-3">
                @foreach ($vesselLinks as $vessel)
                    <li class="list-group-item">
                        <a href="{{ route($vessel['route']) }}">{{ $vessel['name'] }}</a> (IMO: {{ $vessel['imo'] }})
                        <p class="text-muted mb-0">{{ $vessel['status'] }}</p>
                    </li>
                @endforeach
            </ul>
        @endif

        <h5>Sources</h5>
        <ul class="list-unstyled">
            <li><a href="https://www.vesselfinder.com/" target="_blank">VesselFinder</a> - Real-time AIS ship tracking</li>
        </ul>
    </div>
@endsection