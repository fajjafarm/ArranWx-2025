@extends('layouts.vertical', ['title' => 'Ship AIS Map'])

@section('content')
    @include('layouts.partials.page-title', ['subtitle' => 'Resources', 'title' => 'Ships near Arran'])

    <div class="container">
        <h4>Ships Near Arran</h4>
        @if (isset($error))
            <div class="alert alert-danger" role="alert">{{ $error }}</div>
        @endif
        @if (!empty($mapParams))
            <iframe
                src="https://www.vesselfinder.com/?lat={{ $mapParams['latitude'] }}&lon={{ $mapParams['longitude'] }}&zoom={{ $mapParams['zoom'] }}&names={{ $mapParams['names'] ? 'true' : 'false' }}"
                style="width: {{ $mapParams['width'] }}; height: {{ $mapParams['height'] }}px; border: none;"
                frameborder="0"
                allowfullscreen
            ></iframe>
        @else
            <p class="text-muted">Map unavailable.</p>
        @endif

        @if (!empty($vesselLinks))
            <h5>Tracked Vessels</h5>
            <ul class="list-group mt-3">
                @foreach ($vesselLinks as $vessel)
                    <li class="list-group-item">
                        <a href="{{ route($vessel['route']) }}">{{ $vessel['name'] }}</a>
                        <p class="text-muted mb-0">{{ $vessel['status'] }}</p>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection