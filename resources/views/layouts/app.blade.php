<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'WayWay')</title>

    {{-- Gunakan Vite untuk CSS & JS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans">

    {{-- Navbar --}}
    @include('wisatawan.components.navbar')

    {{-- Content --}}
    @yield('content')

    {{-- Footer --}}
    @include('wisatawan.components.footer')
</body>
</html>
