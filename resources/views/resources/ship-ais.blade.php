@extends('layouts.vertical', ['title' => 'Ship AIS Map'])

@section('content')
    @include('layouts.partials.page-title', ['subtitle' => 'Resources', 'title' => 'Ship AIS Map'])

    <div class="container">
        <iframe src="{{ $mapUrl }}" width="100%" height="600px" frameborder="0"></iframe>
    </div>
@endsection