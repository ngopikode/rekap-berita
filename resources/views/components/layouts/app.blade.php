<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="{{ Auth::user()->theme_mode ?? 'light' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Niema Dashboard') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    @include('layouts.sections.styles')

    @stack('custom-styles')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div id="wrapper">

    <!-- Desktop Sidebar (Hidden on Mobile) -->
    <div class="d-none d-md-flex">
        <livewire:layout.sidebar elementId="sidebar-wrapper"/>
    </div>

    <!-- Mobile Offcanvas Sidebar -->
    <div class="offcanvas offcanvas-start d-md-none" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel" style="width: 280px;">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title font-serif fw-bold" id="mobileSidebarLabel">{{ config('app.name') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0">
            <livewire:layout.sidebar elementId="mobile-sidebar-wrapper"/>
        </div>
    </div>

    <div id="page-content-wrapper">
        <livewire:layout.navigation :header="$header ?? null"/>

        <main class="container-fluid px-4 py-4">
            {{ $slot }}
        </main>
    </div>

    @include('layouts.sections.scripts')

    @stack('custom-scripts')

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('theme-updated', (event) => {
                document.documentElement.setAttribute('data-bs-theme', event.theme);
            });
        });
    </script>
</div>
</body>
</html>
