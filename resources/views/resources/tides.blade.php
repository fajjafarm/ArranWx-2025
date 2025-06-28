@extends('layouts.vertical', ['title' => $location->name . ' Tide Forecast'])

@section('html-attribute')
    data-sidenav-size="full"
@endsection

@section('css')
    @vite(['resources/scss/app.scss', 'node_modules/flatpickr/dist/flatpickr.min.css'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/weather-icons/2.0.10/css/weather-icons.min.css">
    <style>
        .tide-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .tide-table th,
        .tide-table td {
            padding: 8px;
            text-align: center;
            border-bottom: 1px solid #ddd;
            font-size: 14px;
            vertical-align: middle;
        }
        .tide-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .day-header {
            background-color: #e9ecef;
            font-size: 16px;
            padding: 10px;
            text-align: left;
        }
        .tide-icon {
            font-size: 24px;
            color: #555;
        }
        .tide-height {
            background: transparent;
        }
        .tide-height.high {
            background: #99ebff;
        }
        .tide-height.low {
            background: #ffcc80;
        }
        @media (max-width: 768px) {
            .tide-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
            .tide-table th,
            .tide-table td {
                padding: 6px;
                font-size: 12px;
            }
            .tide-icon {
                font-size: 18px;
            }
            .day-header {
                font-size: 14px;
            }
        }
    </style>
@endsection

@section('content')
    @include('layouts.partials.page-title', ['subtitle' => 'Tide Details', 'title' => $location->name])

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">{{ $location->name }} 7-Day Tide Forecast</h2>
                    <p class="text-muted">
                        {{ $location->description ?? 'Tide forecast for ' . $location->name . ', located at latitude ' . $location->latitude . ', longitude ' . $location->longitude . '.' }}
                    </p>

                    @if (!empty($tideData['hourly']))
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h6 class="text-muted fs-14 text-center">7-Day Sea Level Height Forecast</h6>
                                <canvas id="tideChart" style="max-height: 200px; margin: 20px auto;"></canvas>
                            </div>
                        </div>

                        @foreach ($tideData['hourly'] as $date => $tides)
                            <div class="day-header">
                                {{ \Carbon\Carbon::parse($date)->format('l, M d') }}
                            </div>
                            <table class="tide-table">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Type</th>
                                        <th>Height (m)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tides as $tide)
                                        @php
                                            $type = $tide['sea_level_height_msl'] > 0 ? 'High' : 'Low';
                                            $heightClass = $type === 'High' ? 'high' : 'low';
                                        @endphp
                                        <tr>
                                            <td>{{ $tide['time'] }}</td>
                                            <td>
                                                <i class="wi {{ $type === 'High' ? 'wi-flood' : 'wi-horizon-alt' }} tide-icon" title="{{ $type }} Tide"></i>
                                                {{ $type }}
                                            </td>
                                            <td class="tide-height {{ $heightClass }}">{{ number_format($tide['sea_level_height_msl'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endforeach
                    @else
                        <p class="text-muted mt-4">No tide data available.</p>
                    @endif

                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-light mt-3">Back to Dashboard</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @vite(['resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @php
                $chartLabels = [];
                $tideData = [];
                if (!empty($tideData['hourly'])) {
                    foreach ($tideData['hourly'] as $date => $tides) {
                        $chartLabels[] = \Carbon\Carbon::parse($date)->format('M d');
                        $tideData[] = collect($tides)->avg('sea_level_height_msl');
                    }
                }
            @endphp
            @if (!empty($chartLabels))
                const chartLabels = @json($chartLabels);
                const tideData = @json($tideData);
                const tideCtx = document.getElementById('tideChart').getContext('2d');
                new Chart(tideCtx, {
                    type: 'line',
                    data: {
                        labels: chartLabels,
                        datasets: [{
                            label: 'Average Sea Level Height (m)',
                            data: tideData,
                            borderColor: '#3498db',
                            backgroundColor: 'rgba(52, 152, 219, 0.2)',
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { title: { display: true, text: 'Sea Level Height (m)' } },
                            x: { title: { display: true, text: 'Date' } }
                        },
                        plugins: { legend: { display: true, position: 'top' } }
                    }
                });
            @endif
        });
    </script>
@endsection