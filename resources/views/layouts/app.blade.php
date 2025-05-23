<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Kasir</title>

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#ffffff">
    <meta name="description" content="DS Mart - Sistem Kasir Modern">
    <link rel="manifest" href="{{ asset('/manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/img/logo.png') }}">

    @vite(['resources/js/app.js', 'resources/css/app.css'])
    <link rel="icon" href="{{ asset('assets/img/logo.png') }}">
    <!-- Font Awesome CDN untuk icon -->
    <link rel="stylesheet" href="{{ asset('assets/fontawesome-free-6.7.2-web/css/all.min.css') }}">
    <!-- DataTables CDN -->
    <link rel="stylesheet" href="{{ asset('assets/datatables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/toastr/build/toastr.min.css') }}">
    <script src="{{ asset('assets/js/jquery.js') }}"></script>
    <script src="{{ asset('assets/datatables/datatables.min.js') }}"></script>

    <!-- PWA Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js').then(function(registration) {
                    console.log('ServiceWorker registration successful');
                }, function(err) {
                    console.log('ServiceWorker registration failed: ', err);
                });
            });
        }
    </script>
</head>

<body class="bg-gradient-to-br from-indigo-50 to-purple-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        @Navbar()

        @yield('content')
    </div>

    <script src="{{ asset('assets/toastr/build/toastr.min.js') }}"></script>
    @stack('scripts')

</body>

</html>
