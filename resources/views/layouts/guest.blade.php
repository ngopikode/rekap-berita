<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'EzMenu Enterprise') }}</title>

    <!-- Fonts: Plus Jakarta Sans (Enterprise Standard) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
          rel="stylesheet">

    <!-- Scripts (Vite) -->
    @vite(['resources/css/guest.css', 'resources/js/app.js'])
</head>

<body class="antialiased">

<!-- Background Element -->
<div class="bg-mesh"></div>

<div class="min-vh-100 d-flex flex-column justify-content-center align-items-center py-5 px-3">

    <!-- Main Content Slot -->
    <!-- Branding is now handled inside the login component for better composition -->
    <div class="w-100" style="max-width: 460px;">
        {{ $slot }}
    </div>

    <!-- Minimal Footer -->
    <div class="mt-5 text-center">
        <p class="small text-muted mb-0 opacity-75">
            &copy; {{ date('Y') }} ngopikode Enterprise. <br>
            <span style="font-size: 0.75rem;">Authorized Personnel Only</span>
        </p>
    </div>

</div>

</body>
</html>
