@extends('app1')

@section('content')
<style>
    body {
        background: linear-gradient(to bottom right, #e0f7fa, #b2ebf2);
    }
</style>

<div class="flex justify-center items-start pt-2 pb-10 px-4 min-h-screen">
    <div class="w-full max-w-2xl bg-white rounded-2xl shadow-xl p-6">
        <h1 class="text-2xl font-bold mb-6 text-center text-blue-900">Generate Water Usage Report</h1>

        <form action="{{ route('reports.generate') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="report_type" class="block text-gray-700 font-medium mb-5 px-2 pt-5">Report Type</label>
                <select name="report_type" id="report_type" class="w-full p-2 border border-gray-300 rounded-lg" required>
                    <option value="daily">Daily Report</option>
                    <option value="weekly">Weekly Report</option>
                    <option value="monthly">Monthly Report</option>
                    <option value="yearly">Annual Report</option>
                    <option value="custom">Custom Date Range</option>
                </select>
            </div>

            <div id="custom_date_range" class="space-y-3 hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="start_date" class="block text-gray-700 font-medium mb-5 py-2 px-2">Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="w-full p-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label for="end_date" class="block text-gray-700 font-medium mb-5 px-2">End Date</label>
                        <input type="date" name="end_date" id="end_date" class="w-full p-2 border border-gray-300 rounded-lg">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-2 px-2">Output Format</label>
                <div class="flex items-center gap-6">
                    <label class="inline-flex items-center px-2">
                        <input type="radio" name="format" value="html" checked class="form-radio text-blue-500">
                        <span class="ml-2 text-gray-700">Web View</span>
                    </label>
                </div>
            </div>

            <div class="text-center pt-4">
                <button type="submit" class="bg-blue-600 text-black font-semibold px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                    Generate Report
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('report_type').addEventListener('change', function () {
        document.getElementById('custom_date_range').classList.toggle('hidden', this.value !== 'custom');
    });
</script>
@endsection
