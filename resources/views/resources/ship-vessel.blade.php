@extends('layouts.vertical', ['title' => $vesselName])

@section('content')
    @include('layouts.partials.page-title', ['subtitle' => 'Resources', 'title' => $vesselName])

    <div class="container">
        <h4>{{ $vesselName }} Tracking</h4>
        @if (isset($error))
            <div class="alert alert-danger" role="alert">{{ $error }}</div>
        @endif
        @if (!empty($mapParams))
            <iframe
                src="https://www.vesselfinder.com/?lat={{ $mapParams['latitude'] }}&lon={{ $mapParams['longitude'] }}&zoom={{ $mapParams['zoom'] }}&mmsi={{ $mapParams['mmsi'] }}&names={{ $mapParams['names'] ? 'true' : 'false' }}"
                style="width: {{ $mapParams['width'] }}; height: {{ $mapParams['height'] }}px; border: none;"
                frameborder="0"
                allowfullscreen
            ></iframe>
            @if (isset($vesselStatus))
                <p class="text-muted mt-2">{{ $vesselStatus }}</p>
            @endif
        @else
            <p class="text-muted">Map unavailable.</p>
        @endif
    </div>
@endsection