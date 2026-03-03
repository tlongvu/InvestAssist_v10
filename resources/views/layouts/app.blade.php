<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'InvestAssist') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">


        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body {
                font-family: 'IBM Plex Sans', sans-serif;
            }
            /* Applied instantly by inline script below to prevent flash */
            .privacy-active .private-number {
                visibility: hidden !important;
            }
        </style>

        {{-- Inline script: runs SYNCHRONOUSLY before page renders to prevent number flash --}}
        <script>
            (function () {
                try {
                    if (localStorage.getItem('investassist_privacy') === 'true') {
                        document.documentElement.classList.add('privacy-active');
                    }
                } catch (e) {}
            })();
        </script>
    </head>
    <body class="font-sans antialiased bg-[#FAFAFA] text-[#09090B]">
        <!-- Page Wrapper -->
        <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">
            
            <!-- Sidebar -->
            @include('layouts.sidebar')

            <!-- Main Content Area -->
            <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden pt-2 px-2 pb-2 md:pt-4 md:pr-4 md:pb-4 md:pl-0">
                
                <!-- Main Header (Top bar) -->
                @include('layouts.topbar')

                <!-- Page Content -->
                <main class="w-full grow mt-4">
                    {{ $slot }}
                </main>

            </div>
        </div>
    </body>
</html>
