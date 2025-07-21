<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Household Water Usage Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <style>
        .page-header {
            padding: 20px;
            text-align: center;
            background-color: #3b82f6;
            color: white;
            margin-bottom: 20px;
        }
        .page-header h1 {
            margin: 0;
            font-size: 24px;
        }

        /* Keep all your existing styles below */
        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            padding: 20px;
            background-color: #f8fafc;
        }
        .chart-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            padding: 20px;
            border-left: 4px solid #3b82f6;
        }
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
        .flow-meter {
            display: flex;
            align-items: center;
            height: 100px;
            background: #f0f4f8;
            border-radius: 8px;
            padding: 10px;
            margin-top: 15px;
            position: relative;
        }
        .flow-level {
            height: 80px;
            background: linear-gradient(to top, #3b82f6, #60a5fa);
            border-radius: 4px;
            transition: width 0.5s ease;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            color: white;
            font-weight: bold;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
        }
        .flow-labels {
            display: flex;
            justify-content: space-between;
            margin-top: 5px;
            font-size: 12px;
            color: #64748b;
        }
        .flow-zones {
            position: absolute;
            width: calc(100% - 20px);
            height: 80px;
            display: flex;
            pointer-events: none;
        }
        .flow-zone {
            height: 100%;
            opacity: 0.1;
        }
        h4 {
            color: #1e293b;
            margin-bottom: 15px;
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        h4 svg {
            margin-right: 8px;
            color: #3b82f6;
        }
        .legend {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
            font-size: 13px;
        }
        .legend-item {
            display: flex;
            align-items: center;
        }
        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 3px;
            margin-right: 5px;
        }
        .no-data {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            color: #64748b;
            font-style: italic;
            background-color: #f8fafc;
            border-radius: 8px;
        }
        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .time-period {
            font-size: 12px;
            color: #64748b;
            background: #f1f5f9;
            padding: 4px 8px;
            border-radius: 4px;
        }
    </style>
</head>
<body>

<form method="GET" action="{{ route('charts') }}" style="margin: 0 20px 20px;">
    <label for="user_id">Filter by User:</label>
    <select name="user_id" onchange="this.form.submit()">
        <option value="">All Users</option>
        @foreach($users as $user)
            <option value="{{ $user->id }}" {{ $selectedUserId == $user->id ? 'selected' : '' }}>
                {{ $user->firstname }} {{ $user->lastname }}
            </option>
        @endforeach
    </select>
</form>

<div class="page-header">
    <h1>Household Water Usage Analytics Dashboard</h1>
</div>
<div class="dashboard">
    <!-- Daily Usage Line Chart -->
    <div class="chart-card">
        <div class="chart-header">
            <h4>
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0-18 0"></path>
                    <path d="M12 7v5l3 3"></path>
                </svg>
                Daily Water Consumption
            </h4>
            <span class="time-period">Last 7 Days</span>
        </div>
        <div class="chart-container">
            @if($usageByDay->isNotEmpty())
                <canvas id="dailyUsageChart"></canvas>
                <div class="legend">
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: rgba(59, 130, 246, 0.2);"></div>
                        <span>Household Usage</span>
                    </div>
                </div>
            @else
                <div class="no-data">No household consumption data available</div>
            @endif
        </div>
    </div>

    <!-- Top Household Consumers Chart -->
    <div class="chart-card">
        <h4>
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
            Top Household Consumers
        </h4>
        <div class="chart-container">
            @if($topConsumers->isNotEmpty())
                <canvas id="topConsumersChart"></canvas>
                <div class="legend">
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: rgba(59, 130, 246, 0.7);"></div>
                        <span>Normal Usage (≤500L)</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: rgba(239, 68, 68, 0.7);"></div>
                        <span>High Usage (>500L)</span>
                    </div>
                </div>
            @else
                <div class="no-data">No household consumption data available</div>
            @endif
        </div>
    </div>

    <!-- Flow Meter Visualization -->
    <div class="chart-card">
        <h4>
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14"></path>
                <path d="M12 5v14"></path>
            </svg>
            Current Flow Rate
        </h4>
        <div class="flow-meter">
            <div class="flow-zones">
                <div class="flow-zone" style="width: 50%; background-color: #3b82f6;"></div>
                <div class="flow-zone" style="width: 25%; background-color: #f59e0b;"></div>
                <div class="flow-zone" style="width: 25%; background-color: #ef4444;"></div>
            </div>
            <div class="flow-level" id="flowMeter" style="width: 45%">
                45 L/min
            </div>
        </div>
        <div class="flow-labels">
            <span>0</span>
            <span>25</span>
            <span>50</span>
            <span>75</span>
            <span>100 L/min</span>
        </div>
        <div class="legend" style="margin-top: 15px;">
            <div class="legend-item">
                <div class="legend-color" style="background-color: #3b82f6;"></div>
                <span>Normal (0-50L/min)</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background-color: #f59e0b;"></div>
                <span>Warning (50-75L/min)</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background-color: #ef4444;"></div>
                <span>Critical (>75L/min)</span>
            </div>
        </div>
    </div>

    <!-- Usage Categories Chart -->
    <div class="chart-card">
        <h4>
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 2v14a2 2 0 0 0 2 2h14"></path>
                <path d="M18 22V8a2 2 0 0 0-2-2H2"></path>
            </svg>
            Usage Categories
        </h4>
        <div class="chart-container">
            @if(($normalUsage + $excessiveUsage) > 0)
                <canvas id="usageCategoriesChart"></canvas>
                <div class="legend">
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: rgba(16, 185, 129, 0.7);"></div>
                        <span>Normal Usage (≤500L)</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: rgba(239, 68, 68, 0.7);"></div>
                        <span>Excessive Usage (>500L)</span>
                    </div>
                </div>
            @else
                <div class="no-data">No usage category data available</div>
            @endif
        </div>
    </div>
</div>
labels: {!! json_encode($usageByDay->pluck('day')->map(function($date) {
    return \Carbon\Carbon::parse($date)->format('M j');
})) !!},

<script>
document.addEventListener('DOMContentLoaded', function() {
    @if($usageByDay->isNotEmpty())
    // Daily Usage Line Chart
    const dailyCtx = document.getElementById('dailyUsageChart').getContext('2d');
    new Chart(dailyCtx, {
        type: 'line',

            datasets: [{
                label: 'Household Water Usage (Liters)',
                data: {!! json_encode($usageByDay->pluck('total_usage')) !!},
                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 2,
                tension: 0.1,
                fill: true,
                pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y.toLocaleString() + ' liters';
                        },
                        title: function(context) {
                            return 'Household usage on ' + context[0].label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Liters', color: '#64748b' },
                    grid: { color: 'rgba(203, 213, 225, 0.3)' },
                    ticks: { color: '#64748b' }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#64748b' }
                }
            }
        }
    });
    @endif

    @if($topConsumers->isNotEmpty())
    // Top Consumers Chart
    const topCtx = document.getElementById('topConsumersChart').getContext('2d');
    new Chart(topCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($topConsumers->pluck('name')) !!},
            datasets: [{
                label: 'Water Usage (Liters)',
                data: {!! json_encode($topConsumers->pluck('usage')) !!},
                backgroundColor: function(context) {
                    return context.raw > 500 ? 'rgba(239, 68, 68, 0.7)' : 'rgba(59, 130, 246, 0.7)';
                },
                borderWidth: 1,
                borderColor: '#fff',
                borderRadius: 4
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.parsed.x.toLocaleString() + ' liters';
                        },
                        afterLabel: function(context) {
                            const data = {!! json_encode($topConsumers) !!}[context.dataIndex];
                            return `Area: ${data.area}\nStatus: ${data.usage > 500 ? 'High Usage' : 'Normal Usage'}`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    title: { display: true, text: 'Liters', color: '#64748b' },
                    grid: { color: 'rgba(203, 213, 225, 0.3)' },
                    ticks: { color: '#64748b' }
                },
                y: {
                    grid: { display: false },
                    ticks: { color: '#64748b' }
                }
            }
        }
    });
    @endif

    @if(($normalUsage + $excessiveUsage) > 0)
    // Usage Categories Chart
    const catCtx = document.getElementById('usageCategoriesChart').getContext('2d');
    new Chart(catCtx, {
        type: 'doughnut',
        data: {
            labels: ['Normal Usage (≤500L)', 'Excessive Usage (>500L)'],
            datasets: [{
                data: [{!! $normalUsage !!}, {!! $excessiveUsage !!}],
                backgroundColor: [
                    'rgba(16, 185, 129, 0.7)',
                    'rgba(239, 68, 68, 0.7)'
                ],
                borderWidth: 1,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((context.raw / total) * 100);
                            return `${context.label}: ${context.raw.toLocaleString()} liters (${percentage}%)`;
                        }
                    }
                },
                datalabels: {
                    formatter: (value, ctx) => {
                        const total = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                        const percentage = Math.round((value / total) * 100);
                        return `${percentage}%`;
                    },
                    color: '#fff',
                    font: { weight: 'bold' }
                }
            }
        },
        plugins: [ChartDataLabels]
    });
    @endif

    // Flow Meter Simulation
    function updateFlowMeter() {
        const baseFlow = Math.floor(Math.random() * 40);
        let flowRate = baseFlow;

        if (Math.random() < 0.2) {
            flowRate += Math.floor(Math.random() * 40) + 10;
        }

        flowRate = Math.min(flowRate, 100);

        const meter = document.getElementById('flowMeter');
        if (meter) {
            meter.style.width = flowRate + '%';
            meter.textContent = flowRate + ' L/min';

            if (flowRate > 75) {
                meter.style.background = 'linear-gradient(to top, #ef4444, #f87171)';
            } else if (flowRate > 50) {
                meter.style.background = 'linear-gradient(to top, #f59e0b, #fbbf24)';
            } else {
                meter.style.background = 'linear-gradient(to top, #3b82f6, #60a5fa)';
            }
        }
    }

    updateFlowMeter();
    setInterval(updateFlowMeter, 2000);
});
</script>
</body>
</html>
