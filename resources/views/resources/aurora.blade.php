@extends('layouts.vertical', ['title' => 'Aurora Borealis Forecast'])

@section('content')
    @include('layouts.partials.page-title', ['subtitle' => 'Resources', 'title' => 'Aurora Borealis Forecast'])

    <div class="container">
        <h4>3-Day Aurora Forecast (June 1–3, 2025)</h4>
        @if ($auroraData['message'])
            <div class="alert alert-info" role="alert">
                {{ $auroraData['message'] }}
            </div>
        @endif

        @if (!empty($auroraData['kp_forecast']))
            <!-- Chart.js Bar Chart -->
            <canvas id="kp-chart" style="max-height: 400px; margin-bottom: 20px;"></canvas>
            <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.js"></script>
            <script>
                var ctx = document.getElementById('kp-chart').getContext('2d');
                var kpData = @json($auroraData['kp_forecast']);
                var labels = kpData.map(item => item.label);
                var kpValues = kpData.map(item => item.kp);

                var chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Kp Index',
                            data: kpValues,
                            backgroundColor: kpValues.map(kp => {
                                if (kp >= 7) return 'rgba(255, 99, 132, 0.7)'; // Strong (G3–G5)
                                if (kp >= 5) return 'rgba(255, 159, 64, 0.7)'; // Moderate (G1–G2)
                                if (kp >= 4) return 'rgba(54, 162, 235, 0.7)'; // Minor
                                return 'rgba(75, 192, 192, 0.7)'; // Low
                            }),
                            borderColor: 'rgba(0, 0, 0, 0.1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 9,
                                title: { display: true, text: 'Kp Index' }
                            },
                            x: {
                                title: { display: true, text: 'Date & Time (BST)' }
                            }
                        },
                        plugins: {
                            annotation: {
                                annotations: {
                                    line4: {
                                        type: 'line',
                                        yMin: 4,
                                        yMax: 4,
                                        borderColor: 'rgba(54, 162, 235, 0.5)',
                                        borderWidth: 2,
                                        label: {
                                            content: 'Northern Scotland (~58°N)',
                                            enabled: true,
                                            position: 'start'
                                        }
                                    },
                                    line5: {
                                        type: 'line',
                                        yMin: 5,
                                        yMax: 5,
                                        borderColor: 'rgba(255, 159, 64, 0.5)',
                                        borderWidth: 2,
                                        label: {
                                            content: 'Central UK (~54°N)',
                                            enabled: true,
                                            position: 'start'
                                        }
                                    },
                                    line7: {
                                        type: 'line',
                                        yMin: 7,
                                        yMax: 7,
                                        borderColor: 'rgba(255, 99, 132, 0.5)',
                                        borderWidth: 2,
                                        label: {
                                            content: 'Southern UK (~50°N)',
                                            enabled: true,
                                            position: 'start'
                                        }
                                    }
                                }
                            },
                            legend: { display: false }
                        }
                    }
                });
            </script>

            <!-- Kp Strength Table -->
            <h5>Kp Index and Aurora Visibility</h5>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Kp</th>
                        <th>G-Scale</th>
                        <th>Visibility in UK</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>0–1</td><td>G0</td><td>Unlikely anywhere</td><td>Quiet, no aurora visible.</td></tr>
                    <tr><td>2–3</td><td>G0</td><td>Far northern Scotland</td><td>Weak activity, faint aurora possible.</td></tr>
                    <tr><td>4</td><td>G0</td><td>Northern Scotland (~58°N)</td><td>Minor activity, aurora visible in north.</td></tr>
                    <tr><td>5</td><td>G1</td><td>Scotland, Northern Ireland (~54°N)</td><td>Minor storm, aurora in northern UK.</td></tr>
                    <tr><td>6</td><td>G2</td><td>Central UK (~53°N)</td><td>Moderate storm, bright aurora in north, visible in central areas.</td></tr>
                    <tr><td>7</td><td>G3</td><td>Northern England (~52°N)</td><td>Strong storm, aurora widely visible.</td></tr>
                    <tr><td>8</td><td>G4</td><td>Southern UK (~50°N)</td><td>Severe storm, aurora across UK.</td></tr>
                    <tr><td>9</td><td>G5</td><td>Southern UK and beyond</td><td>Extreme storm, vivid aurora nationwide.</td></tr>
                </tbody>
            </table>
        @else
            <p class="text-muted">No aurora forecast data available.</p>
        @endif

        <!-- Source Links -->
        <h5>Sources</h5>
        <ul class="list-unstyled">
            <li><a href="https://www.swpc.noaa.gov/" target="_blank">NOAA Space Weather Prediction Center</a> - Kp Index Forecast</li>
        </ul>
    </div>
@endsection