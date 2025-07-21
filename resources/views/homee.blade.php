<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="60">
    <title>Water Conservation Monitoring System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

</head>
@extends('app1')

@section('title', 'Home Usage Dashboard')

@section('content')
<body class="bg-gray-100">
    <div class="container mx-auto p-10">
        <h1 class="text-3xl font-bold text-center mb-6">Water Usage Household Dashboard</h1>

<!-- conservation espiration -->
<div class="text-center mb-8">
    <p class="text-lg text-gray-600">Track your current usage and explore historical patterns to become a water-saving champion!</p>
</div>

<!-- Filter Form -->
<form method="GET" action="{{ route('homee') }}" class="filter-form1">
    @csrf

    <div class="filter-row1">
        <!-- Year Selection -->
        <div class="form-group">
            <label for="year">Year:</label>
            <select name="year" id="year">
                @for ($y = now()->year - 5; $y <= now()->year + 1; $y++)
                    <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                @endfor
            </select>
        </div>

        <!-- Month Selection -->
        <div class="form-group">
            <label for="month">Month:</label>
            <select name="month" id="month">
                @for ($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                    </option>
                @endfor
            </select>
        </div>

        <!-- Week Selection -->
        <div class="form-group">
            <label for="week">Week:</label>
            <select name="week" id="week">
                <option value="">All Weeks</option>
                @for ($w = 1; $w <= 5; $w++)
                    <option value="{{ $w }}" {{ $selectedWeek == $w ? 'selected' : '' }}>
                        Week {{ $w }}
                    </option>
                @endfor
            </select>
        </div>

        <!-- Day Selection -->
        <div class="form-group">
            <label for="day">Day:</label>
            <select name="day" id="day">
                @foreach (['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                    <option value="{{ $day }}" {{ $selectedDay == $day ? 'selected' : '' }}>
                        {{ $day }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="button-container1">
        <button type="submit" class="btn1 btn-primary">Apply Filters</button>
        <a href="{{ route('homee') }}" class="btn btn-secondary">Reset Filters</a>
    </div>
</form>

<h3 class="days">Water Usage for {{ $selectedDay }}</h3>

<!-- Real-Time Usage Summary -->
<div class="bg-white p-6 rounded-lg shadow-md mb-8">
    <h2 class="text-xl font-semibold mb-4">Real-Time Water Usage for {{ $selectedDay }}</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    @foreach ($totalUsage as $area => $usage)
    <div class="bg-blue-50 p-4 rounded-lg">
        <h3 class="text-lg font-medium">
            @if ($area === 'kitchen')
                <i class="fas fa-faucet"></i> Kitchen
            @elseif ($area === 'bathroom')
                <i class="fas fa-shower"></i> Bathroom
            @elseif ($area === 'garden')
                <i class="fas fa-tint"></i> Garden
            @endif
        </h3>
        <p id="usage-{{ $area }}" data-usage="{{ $usage }}" class="text-2xl font-bold">
    {{ number_format($usage, 2) }} litres
</p>

<p class="text-sm text-gray-600">
    @if ($usage > 0)
        @if ($usage > $averageUsage[$area])
            You used {{ round(($usage - $averageUsage[$area]) / $averageUsage[$area] * 100) }}% more than average.
        @else
            You used {{ round(($averageUsage[$area] - $usage) / $averageUsage[$area] * 100) }}% less than average.
        @endif
    @else
        No water usage recorded for this area today.
    @endif
</p>
    </div>
@endforeach
    </div>
     <li class="nav-item">
<a class="summary" href="{{ route('summary') }}">Summary Usage</a>
    </li>
</div>

<!-- Payment Summary Section -->
<div class="bg-white p-6 rounded-lg shadow-md mb-8 border-l-4 border-yellow-500">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold">
            <i class="fas fa-money-bill-wave mr-2 text-yellow-600"></i>Payment Summary
        </h2>
        <a href="{{ $paymentSummary['payment_link'] }}" class="text-blue-600 hover:text-blue-800">
            View Full Payment Details <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Total Amount Due Card -->
        <div class="bg-yellow-50 p-4 rounded-lg">
            <h3 class="text-lg font-medium">
                <i class="fas fa-coins mr-2"></i>Total Due
                <p>Total Due: {{ App\Helpers\CurrencyHelper::formatZMW($payment->amount_due) }}</p>
            </h3>
            <p class="text-2xl font-bold">{{ number_format($paymentSummary['total_due'], 2) }} ZMW</p>
            <p class="text-sm text-gray-600">
                Rate: {{ ($paymentSummary['rate_per_liter'] * 100) }} ngwee per liter
            </p>
        </div>
        <!-- Payment Deadline -->
        <div class="bg-blue-50 p-4 rounded-lg">
            <h3 class="text-lg font-medium">
                <i class="fas fa-calendar-alt mr-2"></i>Due Date
            </h3>
            <p class="text-2xl font-bold">
                {{ \Carbon\Carbon::parse($paymentSummary['due_date'])->format('jS M Y') }}
            </p>
            <p class="text-sm text-gray-600">
                @if($paymentSummary['days_remaining'] > 0)
                    {{ $paymentSummary['days_remaining'] }} days remaining
                @else
                    Payment overdue!
                @endif
            </p>
        </div>

        <!-- Payment Status -->
        <div class="bg-red-50 p-4 rounded-lg">
            <h3 class="text-lg font-medium">
                <i class="fas fa-exclamation-triangle mr-2"></i>Important Notice
            </h3>
            <p class="text-sm">
                Failure to pay by due date will result in:
            </p>
            <ul class="list-disc pl-5 text-sm mt-1">
                <li>Automatic system shutdown</li>
                <li>50,000 MWK reconnection fee</li>
                <li>Possible water disconnection</li>
            </ul>
        </div>
    </div>
</div>

<!-- Alerts Notification Container -->
<div id="alert-container" class="fixed top-20 right-2 space-y-2 z-50"></div>

<!-- Water-Saving Recommendations -->
<div class="tips-container">
    <h2 class="text-xl font-semibold">Water-Saving Recommendations</h2>

    <div class="tips-wrapper">
        <ul class="tips-list">
            @foreach ($tips as $tip)
                <li class="mb-2">
                    <strong class="block text-lg text-blue-600">{{ $tip['title'] }}</strong>
                    <p class="text-gray-700">{{ $tip['description'] }}</p>
                </li>
            @endforeach
        </ul>
    </div>
</div>

    <!-- Gamification & Progress -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <h2 class="text-xl font-semibold mb-4">Your Progress</h2>

    <!-- Daily Progress -->
    <p class="mb-2">You’ve used <strong id="dailyProgressText">{{ number_format($dailyUsage ?? 0, 2) }}</strong> litres today.</p>
    <div class="bg-gray-300 h-4 rounded-lg overflow-hidden">
        <div id="dailyProgressBar" class="bg-red-500 h-full" style="width: {{ isset($dailyProgress) ? min($dailyProgress, 100) : 0 }}%"></div>
    </div>


    <!-- Weekly Progress -->
    <p class="mt-4">You’ve used <strong id="weeklyProgressText">{{ number_format($weeklyUsage, 2) }}</strong> litres this week.</p>
    <div class="bg-gray-300 h-4 rounded-lg overflow-hidden">
        <div id="weeklyProgressBar" class="bg-blue-500 h-full" style="width: {{ min($weeklyProgress, 100) }}%"></div>
    </div>

    <!-- Monthly Progress -->
    <p class="mt-4">You’ve used <strong id="monthlyProgressText">{{ number_format($monthlyUsage, 2) }}</strong> litres this month.</p>
    <div class="bg-gray-300 h-4 rounded-lg overflow-hidden">
        <div id="monthlyProgressBar" class="bg-green-500 h-full" style="width: {{ min($monthlyProgress, 100) }}%"></div>
    </div>
</div>


        <!-- Usage Breakdown Charts -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <h2 class="text-xl font-semibold mb-4">Usage Breakdown (Per Day)</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Bar Chart -->
                <div>
                    <canvas id="usageBarChart" class="w-full h-64"></canvas>
                </div>
                <!-- Pie Chart -->
                <div>
                    <canvas id="usagePieChart" class="w-full h-64"></canvas>
                </div>
            </div>
        </div>

    </div>



    <!-- Chart.js for Usage Breakdown -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    <script>
// real-time refresh page
setInterval(function () {
    fetch('/api/real-time-usage', {
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        ['kitchen', 'bathroom', 'garden'].forEach(area => {
            const el = document.getElementById('usage-' + area);
            if (el) {
                el.innerText = data[area] + ' litres';
                el.dataset.usage = data[area];
            }
        });
    })
    .catch(error => {
        console.error('Error fetching real-time usage:', error);
    });
}, 60000); // every 1 minute

// progress js code
function updateProgress() {
    const params = new URLSearchParams({
        year: document.querySelector('#year')?.value,
        month: document.querySelector('#month')?.value,
        week: document.querySelector('#week')?.value,
        day: document.querySelector('#day')?.value
    });

    fetch("{{ route('getProgressData') }}?" + params.toString())
        .then(response => response.json())
        .then(data => {
            document.querySelector("#dailyProgressText").innerText = data.dailyUsage + " litres";
            document.querySelector("#weeklyProgressText").innerText = data.weeklyUsage + " litres";
            document.querySelector("#monthlyProgressText").innerText = data.monthlyUsage + " litres";

            document.querySelector("#dailyProgressBar").style.width = Math.min(data.dailyProgress, 100) + "%";
            document.querySelector("#weeklyProgressBar").style.width = Math.min(data.weeklyProgress, 100) + "%";
            document.querySelector("#monthlyProgressBar").style.width = Math.min(data.monthlyProgress, 100) + "%";
        })
        .catch(error => console.error("Error fetching progress data:", error));
}


    // unkown code for now


       document.addEventListener("DOMContentLoaded", function() {
    let totalUsage = @json($totalUsage);

    console.log("Usage Data:", totalUsage); // Debugging

    let labels = Object.keys(totalUsage);
    let data = Object.values(totalUsage);

    if (!labels.length || !data.length) {
        console.log("No data available for charts.");
        return;
    }

    // Bar Chart
    const barCtx = document.getElementById('usageBarChart').getContext('2d');
    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Water Usage (Litres)',
                data: data,
                backgroundColor: ['#3b82f6', '#ef4444', '#10b981'],
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Pie Chart
    const pieCtx = document.getElementById('usagePieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                label: 'Water Usage (Litres)',
                data: data,
                backgroundColor: ['#3b82f6', '#ef4444', '#10b981'],
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' },
                tooltip: { callbacks: { label: (context) => `${context.label}: ${context.raw} litres` } }
            }
        }
    });
});


        //pop up alerts
        document.addEventListener("DOMContentLoaded", function() {
    let alerts = @json($alerts);
    console.log("Alerts Data:", alerts);

    if (!alerts || alerts.length === 0) return;

    let alertContainer = document.getElementById("alert-container");

    // Notify that email was sent (if alerts exist)
    if (alerts.length > 0) {
        let emailNotify = document.createElement("div");
        emailNotify.className = "";
        emailNotify.innerHTML = "";
        alertContainer.appendChild(emailNotify);

        setTimeout(() => {
            emailNotify.classList.add("opacity-0");
            setTimeout(() => emailNotify.remove(), 500);
        }, 3000);
    }

    // Original alert display code
    alerts.forEach((message, index) => {
        let alertDiv = document.createElement("div");
        alertDiv.className = "bg-red-500 text-white p-3 rounded-lg shadow-lg transition transform opacity-0";
        alertDiv.innerHTML = `<strong>${message}</strong>`;

        alertContainer.appendChild(alertDiv);

        setTimeout(() => {
            alertDiv.classList.add("opacity-100");
        }, index * 500);

        setTimeout(() => {
            alertDiv.classList.add("opacity-0");
            setTimeout(() => alertDiv.remove(), 500);
        }, 5000 + index * 500);
    });
});
    </script>
    @endsection
</body>
</html>
