<!DOCTYPE html>
<html>
<head>
    <!-- Head content -->
</head>
<body>
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="list-group">
                <a href="{{ route('index') }}" class="list-group-item list-group-item-action active">
                    Dashboard
                </a>
                <a href="{{ route('usage.pattern') }}" class="list-group-item list-group-item-action">Users Location</a>
                <a href="{{ route('charts') }}" class="list-group-item list-group-item-action">Usage Charts</a>
                <a href="{{ route('history') }}"  class="list-group-item list-group-item-action">Usage History</a>

                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <a href="{{ route('register') }}" class="list-group-item list-group-item-action">Register Household</a>

            </div>
        </div>

        <!-- Main Content -->
        <main class="content-area">
            @yield('content')
        </main>
    </div>
</body>
</html>
