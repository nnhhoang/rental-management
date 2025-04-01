<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    
    <!-- Styles -->
    @vite(['resources/css/app.css'])
</head>
<body>
    <div class="min-h-screen">
        <main>
            <div id="app">
                @yield('content')
            </div>
        </main>
    </div>
    
    <!-- Scripts -->
    @vite(['resources/js/app.js'])
    @yield('scripts')
</body>
</html>