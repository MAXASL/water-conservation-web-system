<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaks</title>
</head>
<body>
@extends('layouts.app')

@section('title', 'Leak Detection Dashboard')

@section('styles')
<link href="{{ asset('css/leaks.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="leak-detection-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1><i class="fas fa-tint"></i> Water Leak Detection System</h1>
        <p class="text-muted">Monitor potential leaks and abnormal water usage patterns</p>
    </div>

    <!-- Alert Cards Container -->
    <div class="alert-cards-container">
        <!-- Leak Detection Card -->
        <div class="alert-card">
            <div class="alert-card-header leak">
                <i class="fas fa-tint"></i>
                <span>Potential Leak Detection</span>
            </div>
            <div class="alert-card-body">
                @if(count($leakSuspects) > 0)
                <table class="alert-table">
                    <thead>
                        <tr>
                            <th>Household</th>
                            <th>Avg Usage</th>
                            <th>Spike Count</th>
                            <th>Last Reading</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leakSuspects as $suspect)
                        <tr class="leak-row">
                            <td>{{ $suspect['user'] }}</td>
                            <td>{{ $suspect['avg_usage'] }} L/min</td>
                            <td>{{ $suspect['spike_count'] }} spikes</td>
                            <td>{{ $suspect['last_reading'] }} L/min</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="empty-state">
                    <i class="fas fa-check-circle"></i>
                    <p>No potential leaks detected</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Continuous Flow Card -->
        <div class="alert-card">
            <div class="alert-card-header flow">
                <i class="fas fa-clock"></i>
                <span>Continuous Flow Alerts</span>
            </div>
            <div class="alert-card-body">
                @if(count($continuousFlow) > 0)
                <table class="alert-table">
                    <thead>
                        <tr>
                            <th>Household</th>
                            <th>Duration</th>
                            <th>Average Flow</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($continuousFlow as $flow)
                        <tr class="flow-row">
                            <td>{{ $flow['user'] }}</td>
                            <td>{{ $flow['duration'] }}</td>
                            <td>{{ $flow['avg_flow'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="empty-state">
                    <i class="fas fa-check-circle"></i>
                    <p>No continuous flow detected</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Real-time Monitoring Card -->
    <div class="chart-card">
        <div class="chart-card-header">
            <i class="fas fa-chart-line"></i>
            <span>Real-time Flow Monitoring</span>
        </div>
        <div class="chart-card-body">
            <canvas id="flowChart"></canvas>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Real-time flow chart using Chart.js
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('flowChart').getContext('2d');
    const flowChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Flow Rate (L/min)',
                data: [],
                borderColor: '#3498db',
                backgroundColor: 'rgba(52, 152, 219, 0.1)',
                borderWidth: 2,
                tension: 0.1,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Flow Rate (L/min)',
                        color: '#7f8c8d'
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            }
        }
    });

    // Simulate real-time data
    setInterval(() => {
        const now = new Date();
        flowChart.data.labels.push(now.toLocaleTimeString());
        flowChart.data.datasets[0].data.push(Math.random() * 30);

        if (flowChart.data.labels.length > 15) {
            flowChart.data.labels.shift();
            flowChart.data.datasets[0].data.shift();
        }

        flowChart.update();
    }, 2000);
});
</script>
@endsection

</body>
</html>
