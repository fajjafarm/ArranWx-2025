@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Planes Near Arran</h1>
        <iframe src="{{ $mapUrl }}" width="100%" height="600px" frameborder="0"></iframe>
    </div>
@endsection