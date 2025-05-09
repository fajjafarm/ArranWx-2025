@extends('layouts.vertical', ['title' => 'Planes Near Arran'])

@section('content')
    @include('layouts.partials.page-title', ['subtitle' => 'Resources', 'title' => 'Planes Near Arran'])

    <div class="container">
        <iframe src="{{ $mapUrl }}" width="100%" height="600px" frameborder="0"></iframe>
    </div>
@endsection