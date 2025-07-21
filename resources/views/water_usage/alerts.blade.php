<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Water Usage Alerts</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        .alert-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .alert-table th, .alert-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .alert-table th {
            background-color: #007BFF;
            color: white;
        }
        .alert-table tr:hover {
            background-color: #f5f5f5;
        }
        .alert-actions {
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-alert {
            background-color: #ff6b6b;
            color: white;
        }
        .btn-report {
            background-color: #48dbfb;
            color: white;
        }
        .filter-form {
            background: #f8f9fa;
            padding: 13px;
            border-radius: 5px;
            margin-bottom: 20px;
            margin-top: 60px;
        }
        .filter-group {
            margin-bottom: 10px;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            color: #666;
        }
        .alert-success {
            color: #28a745;
            padding: 10px;
            background: #e8f5e9;
            border-radius: 4px;
        }
        .alert-error {
            color: #dc3545;
            padding: 10px;
            background: #ffebee;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    @extends('layouts.app')

    @section('content')
    <div class="dashboard-layout">
        <div class="content-area">
            <div class="dashboard-container">
                <h1>Water Usage Alerts</h1>

                <!-- Filter Form -->
                <form method="GET" action="{{ route('alerts') }}" class="filter-form">
                    <div class="filter-group">
                        <label for="month">Month:</label>
                        <select name="month" id="month">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ request('month', date('m')) == $m ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="year">Year:</label>
                        <select name="year" id="year">
                            @for ($y = now()->year - 5; $y <= now()->year; $y++)
                                <option value="{{ $y }}" {{ request('year', date('Y')) == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="week">Week:</label>
                        <select name="week" id="week">
                            @for ($w = 1; $w <= 5; $w++)
                                <option value="{{ $w }}" {{ request('week') == $w ? 'selected' : '' }}>
                                    Week {{ $w }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <button type="submit" class="btn">Filter</button>
                </form>

                @if(session('success'))
                    <div class="alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert-error">
                        {{ session('error') }}
                    </div>
                @endif

                @if($water_usage->isNotEmpty())
                    <table class="alert-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Usage (L)</th>
                                <th>Alert Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($water_usage as $usage)
                                <tr>
                                    <td>{{ $usage->user->firstname }} {{ $usage->user->lastname }}</td>
                                    <td>{{ $usage->user->email }}</td>
                                    <td>{{ $usage->usages }}</td>
                                    <td>{{ $usage->alert }}</td>
                                    <td class="alert-actions">
                                        <form method="POST" action="{{ route('send.alert', $usage->user->id) }}">
                                            @csrf
                                            <input type="hidden" name="usage" value="{{ $usage->usages }}">
                                            <input type="hidden" name="alert_type" value="alert">
                                            <button type="submit" class="btn btn-alert">Send Alert</button>
                                        </form>
                                                                            </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="no-data">
                        No water usage data available for the selected period
                    </div>
                @endif
            </div>
        </div>
    </div>
    <style>
    .btn-alert {
        background-color: #dc3545;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 4px;
        cursor: pointer;
    }
    .btn-normal {
        background-color: #28a745;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 4px;
        cursor: pointer;
    }
</style>
    @endsection
</body>
</html>
