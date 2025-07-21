<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Water Usage Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body>
    @extends('layouts.app')

    @section('content')
    <div class="dashboard-layout">
        <!-- Sidebar is automatically included from layouts.app -->

        <div class="content-area">
            <div class="adn">
        <h1>Admin Water Monitoring System Dashboard</h1>
        </div>
            <div class="dashboard-container">
                <div class="stats">
                    <h2>Water Usage Dashboard</h2>

                    <!-- Form for filtering -->
                    <form method="GET" action="{{ route('index') }}" class="filter-form1">
                    @csrf
                    <div class="filter-row1">
                        <!-- Household Filter -->
                        <div class="form-group">
                            <label for="household">Household:</label>
                            <select name="household" id="household" class="form-control">
                                <option value="">All Households</option>
                                @foreach($households as $household)
                                    <option value="{{ $household->id }}" {{ request('household') == $household->id ? 'selected' : '' }}>
                                        {{ $household->firstname }} {{ $household->lastname }}
                                        @if($household->house_address)
                                            ({{ $household->house_address }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Existing Month Filter -->
                        <div class="form-group">
                            <label for="month">Month:</label>
                            <select name="month" id="month" class="form-control">
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <!-- Existing Year Filter -->
                        <div class="form-group">
                            <label for="year">Year:</label>
                            <select name="year" id="year" class="form-control">
                                @for ($y = now()->year - 5; $y <= now()->year; $y++)
                                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <!-- Existing Week Filter -->
                        <div class="form-group">
                            <label for="week">Week:</label>
                            <select name="week" id="week" class="form-control">
                                @for ($w = 1; $w <= 5; $w++)
                                    <option value="{{ $w }}" {{ request('week') == $w ? 'selected' : '' }}>
                                        Week {{ $w }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <!-- Submit Button -->
                        <div class="button-container1">
                            <button type="submit" class="btn1 btn-primary">Filter</button>
                        </div>
                    </div>
                </form>
                </div>


                <h3>Water Usage for {{ \Carbon\Carbon::create($year, $month, 1)->format('F, Y') }}</h3>

                <table class="water-usage-table">

                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Date</th>
                            <th>Usage (L)</th>
                            <th>Alerts</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($water_usage->isNotEmpty())
                            <tr class="{{ $water_usage[0]->usages > 500 ? 'high-usage' : '' }}">
                                <td>{{ $water_usage[0]->user->firstname }} {{ $water_usage[0]->user->lastname }}</td>
                                <td>{{ $water_usage[0]->user->email }}</td>
                                <td>{{ $water_usage[0]->user->phone }}</td>
                                <td>{{ $water_usage[0]->user->address }}</td>
                                <td>{{ $water_usage[0]->date_range }}</td>
                                <td>{{ $water_usage[0]->usages }} L</td>
                                <td>{{ is_array($water_usage[0]->alert) ? implode(', ', $water_usage[0]->alert) : $water_usage[0]->alert }}</td>

                            </tr>
                        @else
                            <tr>
                                <td colspan="7" class="text-center">No data available for the selected filters</td>
                            </tr>
                        @endif

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endsection

</body>
</html>
