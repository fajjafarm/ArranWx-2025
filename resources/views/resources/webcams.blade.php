@extends('layouts.vertical', ['title' => 'Arran Webcams'])

@section('content')
    @include('layouts.partials.page-title', ['subtitle' => 'Resources', 'title' => 'Arran Webcams'])

    <div class="container">
        <p>View live webcams from around the Isle of Arran, including ferry terminals and scenic views. Note: Some webcams may require cookies to be enabled or may open in a new tab.</p>

        <div class="row">
            @foreach ($webcams as $webcam)
                <div class="col-md-6 mb-4">
                    <h3>{{ $webcam['title'] }}</h3>
                    <p>Source: {{ $webcam['source'] }}</p>
                    @if (str_contains($webcam['url'], 'twitch.tv'))
                        <iframe
                            src="{{ $webcam['url'] }}"
                            width="100%"
                            height="300px"
                            frameborder="0"
                            scrolling="no"
                            allowfullscreen
                            loading="lazy"
                        ></iframe>
                    @else
                        <p>This webcam is available on an external site. <a href="{{ $webcam['url'] }}" target="_blank" class="btn btn-primary btn-sm">View Webcam</a></p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endsection