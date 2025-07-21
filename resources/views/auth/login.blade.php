<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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

        .toast {
    position: fixed;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%);
    background-color: #e53e3e;
    color: white;
    padding: 12px 24px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    z-index: 1000;
    opacity: 0;
    animation: fadeInOut 3s ease-in-out;
    font-size: 16px;
}

@keyframes fadeInOut {
    0% { opacity: 0; }
    10% { opacity: 1; }
    90% { opacity: 1; }
    100% { opacity: 0; }
}
    </style>

    <nav class="nav">
        <div class="logo2">
            <img src="{{ asset('images/save-water-logo.webp')}}" alt="logo-icon2">
            <span class="logo-text2">Save Water</span>
        </div>
    </nav>

    <div class="container">
        <div class="form-box">
            <h2>Water Conservation Monitoring System</h2>
            <h3>Its Good To Have You Here!!</h3>

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" onsubmit="return validateEmail()">
                @csrf
                <div class="login-container">
                    <div class="top">
                        <header>Please Login</header>
                    </div>

                    <!-- Email -->
                    <div class="input-box">
                        <input type="email" class="input-field" name="email" id="email" placeholder="Email Address" required>
                        <i class="bx bx-user"></i>
                    </div>

                    <!-- Password -->
                    <div class="input-box" style="position: relative;">
                    <input type="password" class="input-field" name="password" id="password" placeholder="Password" required minlength="6">
                    <i class="bx bx-lock-alt"></i>
                    <button type="button" onclick="togglePassword()" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer;">
                        👁️
                    </button>
                </div>
                    <!-- Register Button Centered -->
                    <div class="submit-container">
                        <input type="submit" class="submit" value="Sign In">
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
function showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.innerText = message;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.remove();
    }, 3000);
}

function validateEmail() {
    const emailInput = document.getElementById("email").value;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!emailRegex.test(emailInput)) {
        showToast("❌ Invalid email format.");
        return false; // Prevent form submission
    }

    return true; // Allow submission
}

function togglePassword() {
    const passwordInput = document.getElementById("password");
    const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
    passwordInput.setAttribute("type", type);
}

function togglePassword() {
    const passwordInput = document.getElementById("password");
    const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
    passwordInput.setAttribute("type", type);
}


    function togglePassword() {
        const passwordInput = document.getElementById("password");
        const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
        passwordInput.setAttribute("type", type);
    }
</script>
</body>
</html>
