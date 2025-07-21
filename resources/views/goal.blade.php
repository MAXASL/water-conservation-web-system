<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Water-Saving Goals</title>

    <!-- Link to External CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <!-- Chart.js for water usage comparison -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

@extends('app1')

@section('content')
<body class="bg-gray-100">

<div class="container2">

    <!-- Page Title -->
    <h1 class="page-title">Set & Track Your Water-Saving Goal</h1>

    @if (session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid-container">

        <!-- Goal Setting Section -->
        <div class="goal-section">
            <h2 class="section-title">Set Your Daily Water Usage Goal</h2>
            <form action="{{ route('goals.store') }}" method="POST">
                @csrf
                <label class="form-label">Target Usage (litres per day):</label>
                <input type="number" name="target_usage" class="input-field" placeholder="Enter your daily goal" required>
                <button type="submit" class="submit-btn">Save Goal</button>
            </form>
        </div>

        <!-- Progress & Comparison Section -->
        <div class="progress-section">
            <h2 class="section-title">Your Progress</h2>
            @if ($goal)
                <p>You've used <strong>{{ $progress }}%</strong> of your monthly target!</p>
                <div class="progress-bar2">
                    <div class="progress-fill" style="width: {{ $progress }}%;"></div>
                </div>
            @else
                <p>No goal set yet. Set a goal above.</p>
            @endif

            <h2 class="section-title">Comparison with Others</h2>
            <p>You are using <strong>{{ round($currentUsage - $similarHouseholdsUsage, 2) }} litres</strong>
                {{ $currentUsage > $similarHouseholdsUsage ? 'more' : 'less' }} water than similar households.</p>

            <!-- Water Usage Chart -->
            <div>
                <canvas id="usageChart2"></canvas>
            </div>
        </div>

    </div>

    <!-- Encouragement & Tips Section -->
    <div class="tips2">
        <h2 class="section-title">Keep Going! You're Doing Great! 💧</h2>
        <p>You're on track to save <strong>{{ max(0, 100 - $progress) }}%</strong> this month!</p>

        @if ($progress >= 100)
            <p class="congratulations">🎉 Congratulations! You’ve achieved your goal! 🎉</p>
        @endif

        <h2 class="section-title">Tips for Saving More Water</h2>
        <ul class="tips-list">
            <li>Turn off taps while brushing your teeth.</li>
            <li>Use a water-efficient washing machine.</li>
            <li>Fix leaks as soon as possible.</li>
            <li>Take shorter showers and install water-saving showerheads.</li>
        </ul>
    </div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        var ctx = document.getElementById('usageChart2');

        if (ctx) {
            var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['You', 'Similar Households'],
                    datasets: [{
                        label: 'Water Usage (litres)',
                        data: [{{ $currentUsage }}, {{ $similarHouseholdsUsage }}],
                        backgroundColor: ['rgba(54, 162, 235, 0.6)', 'rgba(75, 192, 192, 0.6)'],
                        borderColor: ['rgba(54, 162, 235, 1)', 'rgba(75, 192, 192, 1)'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        } else {
            console.error("Canvas element not found");
        }
    });
</script>

</body>
@endsection
</html>
