@extends('layouts.vertical', ['title' => 'Ship AIS Map'])

@section('content')
    @include('layouts.partials.page-title', ['subtitle' => 'Resources', 'title' => 'Ships Near Arran'])

    <div class="container">
        <p>View real-time ship movements around the Isle of Arran using VesselFinder's AIS map. Track specific vessels below.</p>
        <iframe src="{{ $mapUrl }}" width="100%" height="600px" frameborder="0" loading="lazy"></iframe>
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