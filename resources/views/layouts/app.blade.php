<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <!-- Load Bootstrap CSS first -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Then your custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="d-flex flex-column min-vh-100">
    @include('layouts.header')

    <main class="flex-grow-1">
        <div class="container-fluid">
            <div class="row">
                <!-- Sidebar -->
                <aside class="col-md-2 col-lg-2 d-md-block sidebar collapse">
                    @include('layouts.sidebar')
                </aside>

                <!-- Main Content -->
                <div class="col-md-10 col-lg-10 ms-sm-auto px-md-4">
                    @yield('content')
                </div>
            </div>
        </div>
    </main>

    @include('layouts.footer')

    <!-- JavaScript at the bottom -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts') <!-- For page-specific scripts -->
</body>
</html>
