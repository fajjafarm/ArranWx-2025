@extends('layouts.vertical', ['title' => 'Dashboard'])

@section('css')
  <!-- Adjust if you have specific CSS -->
    <style>
        .ferry-status-box {
            width: 200px;
            height: 150px;
            margin: 10px;
            display: inline-block;
            vertical-align: top;
        }
        .ferry-status-box .card-body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100%;
            color: #fff; /* White text for contrast */
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5); /* Improve readability */
        }
        .status-normal { background-color: #28a745; }
        .status-yellow { background-color: #ffc107; }
        .status-amber { background-color: #ff8c00; }
        .status-red { background-color: #dc3545; }
    </style>
@endsection

@section('content')
    @include('layouts.partials.page-title', ['subtitle' => 'Overview', 'title' => 'Dashboard'])

    <div class="row">
        <!-- Existing dashboard content (e.g., weather summary) -->
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <h5 class="text-muted fs-13 text-uppercase">Weather Summary</h5>
                    <!-- Existing content here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Ferry Status Section -->
    <div class="row justify-content-center">
        <h5 class="text-muted fs-14 mt-4 mb-3 text-center">CalMac Ferry Status - {{ now()->format('d M Y') }}</h5>

        <!-- Brodick to Ardrossan -->
        <div class="ferry-status-box">
            <div class="card">
                <div class="card-body {{ $brodickArdrossanStatus['class'] }}">
                    <h6 class="mb-1">Brodick - Ardrossan</h6>
                    <p class="mb-0">{{ $brodickArdrossanStatus['text'] }}</p>
                </div>
            </div>
        </div>

        <!-- Brodick to Troon -->
        <div class="ferry-status-box">
            <div class="card">
                <div class="card-body {{ $brodickTroonStatus['class'] }}">
                    <h6 class="mb-1">Brodick - Troon</h6>
                    <p class="mb-0">{{ $brodickTroonStatus['text'] }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @vite(['resources/js/app.js'])
@endsection