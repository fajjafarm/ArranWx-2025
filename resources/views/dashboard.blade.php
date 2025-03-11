@extends('layouts.vertical', ['title' => 'Arran Weather Dashboard'])

@section('html-attribute')
    data-sidenav-size="full"
@endsection

@section('css')
    @vite(['node_modules/flatpickr/dist/flatpickr.min.css'])
    <style>
        .separator {
            margin: 20px 0;
            border-top: 2px solid #ddd;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
            display: flex;
            align-items: center;
            gap: 8px;
        }
    </style>
@endsection

@section('content')
    @include('layouts.partials.page-title', ['subtitle' => 'Weather', 'title' => 'Arran Weather Dashboard'])

    <div class="row">
        <div class="col">
            <!-- Village Locations -->
            <div class="section-title">
                <iconify-icon icon="solar:buildings-bold-duotone" class="fs-24"></iconify-icon>
                Villages
            </div>
            <div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 text-center">
                @foreach ($locations->where('type', 'Village') as $location)
                    <div class="col">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="text-muted fs-13 text-uppercase" title="{{ $location->name }}">
                                    <a href="{{ route('location.show', $location->name) }}" class="link-reset">
                                        {{ $location->name }} ({{ $location->altitude ?? 0 }}m)
                                    </a