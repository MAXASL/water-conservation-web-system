
<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container-fluid">

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
        <div class="logo">
            <img src="{{ asset('images/save-water-logo.webp')}}" alt="logo-icon">
            <span class="logo-text">Save Water</span>
        </div>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="/index">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/alerts">Alerts</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/homee">User</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="leaks">High usage</a>
                </li>

                <div class="logout">
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Logout
                </a>
            </div>
            </ul>
        </div>
    </div>
</nav>
