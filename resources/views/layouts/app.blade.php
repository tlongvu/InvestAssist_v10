<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'InvestAssist') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('logo.svg') }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Flatpickr (Date Picker) -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vn.js"></script> <!-- Vietnamese support -->


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
            /* Flatpickr customization */
            .flatpickr-day.selected {
                background: #2563EB !important;
                border-color: #2563EB !important;
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

            window.formatMoneyInput = function(el) {
                // Save cursor position
                let cursorPosition = el.selectionStart;
                let oldLength = el.value.length;

                // Clean non-numeric except dot
                let value = el.value.replace(/[^0-9.]/g, '');
                
                // Ensure only one dot
                const parts = value.split('.');
                if (parts.length > 2) {
                    value = parts[0] + '.' + parts.slice(1).join('');
                }

                if (value === '') {
                    el.value = '';
                    return;
                }

                // Format with commas for the integer part
                let formattedValue = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                
                // Add decimal part back
                if (parts.length > 1) {
                    formattedValue += "." + parts[1];
                }

                el.value = formattedValue;

                // Restore cursor position
                let newLength = el.value.length;
                el.setSelectionRange(cursorPosition + (newLength - oldLength), cursorPosition + (newLength - oldLength));
            };

            document.addEventListener('input', function(e) {
                if (e.target.classList.contains('money-input')) {
                    window.formatMoneyInput(e.target);
                }
            });

            // Global Datepicker Init
            document.addEventListener('DOMContentLoaded', function() {
                flatpickr(".datepicker", {
                    locale: "vn",
                    dateFormat: "Y-m-d", // Data format
                    altInput: true,
                    altFormat: "d/m/Y",  // Display format
                    allowInput: true
                });
            });
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
