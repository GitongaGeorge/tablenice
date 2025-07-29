<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js']) {{-- Or your equivalent --}}
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen bg-gray-100">
        {{ $slot }}
    </div>

    @livewireScripts
    <script src="//unpkg.com/alpinejs" defer></script>
    {{-- Add any other scripts you need --}}
    @stack('scripts')
</body>
</html>