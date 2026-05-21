<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'WayWay')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')          {{-- ← tambah ini --}}
</head>
<body class="font-sans">

    @include('wisatawan.components.navbar')

    @yield('content')

@include('wisatawan.components.footer')

    <x-waybot />
    @stack('scripts')         {{-- ← tambah ini --}}

</body>
</html>