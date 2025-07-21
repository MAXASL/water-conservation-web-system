<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">

        <!-- Logo on the Left -->
        <div class="logo">
            <img src="{{ asset('images/save-water-logo.webp')}}" alt="logo-icon">
            <span class="logo-text">Save Water</span>
        </div>

        <!-- Mobile Toggle Button -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation Links -->
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="/homee">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('goal.create') }}">Goals</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('leaks.create') }}">Report Leaks</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('incentives') }}">Incentives</a>
                </li>

            <a href="{{ route('reports') }}" class="nav-link">
            <i class=></i> Generate Reports
           </a>
                <!-- Logout -->
                <li class="nav-item logout">
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
