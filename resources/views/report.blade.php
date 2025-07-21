<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report a Water Leak</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
@extends('app1')

@section('content')
<body class="bg-gray-100">
    <div class="container3">
        <h1 class="page-title3">Report a Leak</h1>

        <!-- Success Message -->
        @if (session('success'))
            <div class="success-message3">
                {{ session('success') }}
            </div>
        @endif

        <!-- Leak Reporting Form -->
        <form action="{{ route('leaks.store') }}" method="POST" enctype="multipart/form-data" class="report-form3">
            @csrf
            <!-- Location Input -->
            <label class="form-label3" for="location">Location:</label>
            <select name="location" id="location" class="input-field3">
                <option value="Kitchen">Kitchen</option>
                <option value="Bathroom">Bathroom</option>
                <option value="Garden">Garden</option>
                <option value="Other">Other</option>
            </select>

            <!-- Description Input -->
            <label class="form-label3" for="description">Description:</label>
            <textarea name="description" id="description" class="input-field3" rows="3" placeholder="Describe the leak..."></textarea>

            <!-- Severity Input -->
            <label class="form-label3" for="severity">Severity:</label>
            <select name="severity" id="severity" class="input-field3">
                <option value="Minor">Minor</option>
                <option value="Moderate">Moderate</option>
                <option value="Severe">Severe</option>
            </select>

            <!-- Image Upload -->
            <label class="form-label3" for="image">Upload Image (Optional):</label>
            <input type="file" name="image" id="image" class="input-field3">

            <!-- Contact Information (Optional) -->
            <label class="form-label3" for="contact_info">Your Contact (Optional):</label>
            <input type="text" name="contact_info" id="contact_info" class="input-field3" placeholder="Your phone/email (optional)">

            <!-- Submit Button -->
            <button type="submit" class="submit-btn3">Submit Report</button>
        </form>

        <!-- Why Reporting Leaks is Important -->
        <div class="info-section3">
            <h2 class="section-title3">Why Report Water Leaks?</h2>
            <p>Leaks waste water and increase your bills. Reporting leaks helps:</p>
            <ul class="info-list3">
                <li>Prevent water wastage and environmental damage</li>
                <li>Save money on utility bills</li>
                <li>Ensure timely repairs and prevent property damage</li>
            </ul>
        </div>
    </div>
</body>
@endsection
</html>
