<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History</title>
</head>
<body>
@extends('app1')

@section('title', 'Household Usage Summary')

@section('styles')
<link href="{{ asset('css/usage-history.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="usage-history-container">
    <div class="page-header">
        <h1><i class="fas fa-history"></i> Household Usage Summary <Summary></Summary></h1>
        <p class="subtitle">Detailed water consumption patterns! </p>
    </div>

    <!-- Usage Summary Cards -->
    <div class="summary-cards">
        <div class="summary-card total">
            <div class="card-icon">
                <i class="fas fa-tint"></i>
            </div>
            <div class="card-content">
                <h3>Total Consumption</h3>
                <p>{{ number_format($usageData->sum(function($user) {
                    return $user['usage_days']->sum('total_usage');
                })) }} Liters</p>
            </div>
        </div>

        <div class="summary-card high">
            <div class="card-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="card-content">
                <h3>High Usage Days</h3>
                <p>{{ $usageData->sum(function($user) {
                    return $user['usage_days']->where('is_high', true)->count();
                }) }} Days</p>
            </div>
        </div>

        <div class="summary-card leak">
            <div class="card-icon">
                <i class="fas fa-water"></i>
            </div>
            <div class="card-content">
                <h3>Possible Leaks</h3>
                <p>{{ $usageData->sum(function($user) {
                    return $user['usage_days']->where('is_leak', true)->count();
                }) }} Detected</p>
            </div>
        </div>

        <div class="summary-card optimal">
            <div class="card-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="card-content">
                <h3>Optimal Days</h3>
                <p>{{ $usageData->sum(function($user) {
                    return $user['usage_days']->where('is_optimal', true)->count();
                }) }} Days</p>
            </div>
        </div>
    </div>

    <!-- Detailed Usage Table -->
    <div class="usage-table-card">
        <div class="table-responsive">
            <table class="usage-table">
                <thead>
                    <tr>
                        <th>Household</th>
                        <th>Date</th>
                        <th>Total Usage (L)</th>
                        <th>Avg/Min/Max</th>
                        <th>Status</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usageData as $userData)
                        @foreach($userData['usage_days'] as $day)
                        <tr class="
                            {{ $day['is_leak'] ? 'leak-row' : '' }}
                            {{ $day['is_high'] ? 'high-row' : '' }}
                            {{ $day['is_optimal'] ? 'optimal-row' : '' }}
                        ">
                            <td>
                                <div class="user-info">
                                    <div class="user-name">{{ $userData['user']->firstname }} {{ $userData['user']->lastname }}</div>
                                    <div class="user-address">{{ $userData['user']->area_type }}</div>
                                </div>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($day['date'])->format('M d, Y') }}</td>
                            <td>{{ number_format($day['total_usage']) }}</td>
                            <td>
                                <div class="usage-stats">
                                    <span class="avg">{{ $day['avg_usage'] }}</span>
                                    <span class="min-max">{{ $day['min_usage'] }}-{{ $day['peak_usage'] }}</span>
                                </div>
                            </td>
                            <td>
                                @if($day['is_leak'])
                                    <span class="badge leak-badge">
                                        <i class="fas fa-water"></i> Possible Leak
                                    </span>
                                @elseif($day['is_high'])
                                    <span class="badge high-badge">
                                        <i class="fas fa-exclamation-triangle"></i> High Usage
                                    </span>
                                @elseif($day['is_optimal'])
                                    <span class="badge optimal-badge">
                                        <i class="fas fa-check"></i> Optimal
                                    </span>
                                @else
                                    <span class="badge normal-badge">
                                        <i class="fas fa-check-circle"></i> Normal
                                    </span>
                                @endif
                            </td>
                            <td>
                                <button class="details-button" data-date="{{ $day['date'] }}" data-user="{{ $userData['user']->id }}">
                                    <i class="fas fa-chart-line"></i> View Pattern
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="6" class="empty-state">
                                <i class="fas fa-info-circle"></i>
                                No usage data found for the selected filters
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Daily Usage Pattern Modal -->
    <div class="modal fade" id="usagePatternModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Daily Usage Pattern</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="chart-container">
                        <canvas id="dailyPatternChart"></canvas>
                    </div>
                    <div class="pattern-stats">
                        <div class="stat-card">
                            <i class="fas fa-arrow-up"></i>
                            <span class="stat-label">Peak Hour</span>
                            <span class="stat-value" id="peak-hour">--:--</span>
                        </div>
                        <div class="stat-card">
                            <i class="fas fa-tint"></i>
                            <span class="stat-label">Max Flow</span>
                            <span class="stat-value" id="max-flow">0 L/min</span>
                        </div>
                        <div class="stat-card">
                            <i class="fas fa-clock"></i>
                            <span class="stat-label">Duration</span>
                            <span class="stat-value" id="usage-duration">0 hours</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Initialize modal
    const patternModal = new bootstrap.Modal(document.getElementById('usagePatternModal'));
    let dailyPatternChart = null;

    // Handle details button click
    $('.details-button').click(function() {
        const userId = $(this).data('user');
        const date = $(this).data('date');

        // Fetch hourly data for this user/date
        fetch(`/api/usage/hourly?user_id=${userId}&date=${date}`)
            .then(response => response.json())
            .then(data => {
                // Update modal title
                $('#usagePatternModal .modal-title').text(
                    `Usage Pattern for ${data.user_name} on ${new Date(date).toLocaleDateString()}`
                );

                // Destroy previous chart if exists
                if (dailyPatternChart) {
                    dailyPatternChart.destroy();
                }

                // Create new chart
                const ctx = document.getElementById('dailyPatternChart').getContext('2d');
                dailyPatternChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.hourly_data.map(item => item.hour + ':00'),
                        datasets: [{
                            label: 'Water Flow (L/min)',
                            data: data.hourly_data.map(item => item.usage),
                            borderColor: '#3498db',
                            backgroundColor: 'rgba(52, 152, 219, 0.1)',
                            borderWidth: 2,
                            tension: 0.1,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.parsed.y + ' L/min at ' + context.label;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Flow Rate (L/min)'
                                }
                            }
                        }
                    }
                });

                // Update stats
                const peak = data.hourly_data.reduce((max, item) =>
                    item.usage > max.usage ? item : max, {usage: 0});
                $('#peak-hour').text(peak.hour + ':00');
                $('#max-flow').text(peak.usage + ' L/min');

                const totalHours = data.hourly_data.filter(item => item.usage > 5).length;
                $('#usage-duration').text(totalHours + ' hours');

                // Show modal
                patternModal.show();
            });
    });
});
</script>
@endsection

</body>
</html>
