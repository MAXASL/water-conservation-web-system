@extends('app1')

@section('content')
<style>
    body {
        background: linear-gradient(to bottom right, #d0f0f7, #e3f2fd);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    canvas {
    max-width: 100% !important;
    height: auto !important;
    display: block;
}
</style>

<div class="min-h-screen py-10 px-4 flex justify-center items-start relative z-10">
    <div class="w-full max-w-5xl bg-white rounded-2xl shadow-2xl p-6 sm:p-10">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-blue-800 mb-5 sm:mb-2 pt-5">{{ $title }}</h1>
            <a href="{{ route('reports') }}" class="text-blue-600 hover:underline text-sm">Back to Report Generator</a>
        </div>

        {{-- Summary Boxes --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10">
            <div class="bg-blue-50 p-2 rounded-xl text-center shadow">
                <h3 class="font-semibold text-blue-500 mb-10">Report Period</h3>
                <p class="text-gray-500">{{ $startDate->format('M d, Y') }} – {{ $endDate->format('M d, Y') }}</p>
            </div>
            <div class="bg-green-50 p-5 rounded-xl text-center shadow">
                <h3 class="font-semibold text-green-700 mb-1">Total Water Used</h3>
                <p class="text-gray-500">{{ number_format($totalUsage, 2) }} litres</p>
            </div>
            <div class="bg-purple-50 p-5 rounded-xl text-center shadow">
                <h3 class="font-semibold text-purple-700 mb-1">Average Daily Usage</h3>
                <p class="text-gray-500">{{ number_format($averageDailyUsage, 2) }} litres/day</p>
            </div>
        </div>


        {{-- Tables by Area --}}
        @foreach($groupedData as $area => $dates)
        <div class="mb-10">
            <h2 class="text-xl font-semibold text-gray-800 mb-3 pt-3">{{ ucfirst($area) }} Usage</h2>
            <div class="overflow-x-auto rounded-lg">
                <table class="w-full text-sm border border-gray-300 bg-white shadow-sm">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="py-2 px-3 border">Date/Time</th>
                            <th class="py-2 px-3 border">Usage (litres)</th>
                            <th class="py-2 px-3 border">Flow Rate (L/min)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dates as $date => $entries)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="py-2 px-3 border">{{ $date }}</td>
                            <td class="py-2 px-3 border">{{ number_format($entries->sum('usage'), 2) }}</td>
                            <td class="py-2 px-3 border">{{ number_format($entries->avg('flow_rate'), 2) }}</td>
                        </tr>
                        @endforeach
                        <tr class="bg-gray-50 font-semibold">
                            <td class="py-2 px-3 border">Total</td>
                            <td class="py-2 px-3 border">{{ number_format($dates->flatten()->sum('usage'), 2) }}</td>
                            <td class="py-2 px-3 border">{{ number_format($dates->flatten()->avg('flow_rate'), 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach

        {{-- Chart --}}
        <div class="mt-10 bg-white p-6 rounded-xl shadow-md">
            <h2 class="text-lg font-semibold mb-4 text-gray-800"></h2>
            <canvas id="usageChart" height="50" style="display:block; max-width:100%;"></canvas>
        </div>
    </div>
</div>

