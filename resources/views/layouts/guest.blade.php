<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DS Mart</title>
    <link rel="stylesheet" href="{{ asset('assets/toastr/build/toastr.min.css') }}">
    {{-- icon --}}
    <link rel="icon" href="{{ asset('assets/img/logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-700 min-h-screen flex items-center justify-center">
    @yield('content')
</body>
<script src="{{ asset('assets/js/jquery.js') }}"></script>
<script src="{{ asset('assets/toastr/build/toastr.min.js') }}"></script>
@stack('scripts')

</html>
