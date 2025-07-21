<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>register</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: lightslategray;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container { width: 50%; margin: auto; }
    </style>

    <nav class="nav">
        <div class="logo2">
            <img src="{{ asset('images/save-water-logo.webp')}}" alt="logo-icon2">
            <span class="logo-text2">Save Water</span>
        </div>

        <li class="nav-item">
                    <a class="nav-link" href="{{ route('index') }}">Go Back</a>
                </li>
    </nav>

    <div class="container">
        <div class="form-box">
            <h1>Water Conservation Monitoring System</h1>
            <h3>Please Register The Household</h3>

    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="two-forms">
            <!-- First Name & Last Name -->
            <div class="input-box">
                <input type="text" class="input-field" name="firstname" placeholder="First Name" required>
            </div>
            <div class="input-box">
                <input type="text" class="input-field" name="lastname" placeholder="Last Name" required>
            </div>

            <!-- Email & Phone -->
            <div class="input-box">
                <input type="email" class="input-field" name="email" placeholder="Email" required>
            </div>
            <div class="input-box">
                <input type="text" class="input-field" name="phone" placeholder="Phone Number" required>
            </div>

            <!-- Home Address & Password -->
            <div class="input-box">
                <input type="text" class="input-field" name="address" placeholder="Home Address" required>
            </div>
            <div class="input-box">
                <input type="password" class="input-field" name="password" placeholder="Password (Min: 6 characters)" required minlength="6">
            </div>
        </div>

        <!-- Register Button Centered -->
        <div class="submit-container">
            <input type="submit" class="submit" value="Register Household">
        </div>
    </form>
</div>
</div>
</body>
</html>
