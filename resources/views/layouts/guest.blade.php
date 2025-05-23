<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DS Mart</title>

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#ffffff">
    <meta name="description" content="DS Mart - Sistem Kasir Modern">
    <link rel="manifest" href="{{ asset('/manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/img/logo.png') }}">

    <link rel="stylesheet" href="{{ asset('assets/toastr/build/toastr.min.css') }}">
    {{-- icon --}}
    <link rel="icon" href="{{ asset('assets/img/logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

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

<body class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-700 min-h-screen flex items-center justify-center">
    @yield('content')
</body>
<script src="{{ asset('assets/js/jquery.js') }}"></script>
<script src="{{ asset('assets/toastr/build/toastr.min.js') }}"></script>
@stack('scripts')

</html>
