<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
    <script>
        // This script runs immediately in the <head> to prevent a flash of the wrong theme.
        // It sets the 'dark' class on the <html> element based on localStorage or system preference.
        if (localStorage.getItem('color-theme') === 'dark' ||
            (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles {{-- <-- ADD THIS LINE --}}
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800 transition-colors duration-500">

    <nav class="bg-zinc-100 dark:bg-zinc-900 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-bold text-zinc-900 dark:text-white">My App</span>
                </div>
                <div class="flex items-center">
                    <button id="theme-toggle" type="button"
                        class="text-zinc-500 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700 focus:outline-none focus:ring-4 focus:ring-zinc-200 dark:focus:ring-zinc-700 rounded-lg text-sm p-2.5">
                        <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                        </svg>
                        <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 5.05A1 1 0 003.636 6.464l.707.707a1 1 0 001.414-1.414l-.707-.707zM3 11a1 1 0 100-2H2a1 1 0 100 2h1zM13 17a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1z"
                                fill-rule="evenodd" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <main>
        {{ $slot }}
    </main>

    @fluxScripts

    <script>
        const themeToggleBtn = document.getElementById('theme-toggle');
        const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

        // Function to update the icon visibility based on the current theme
        const updateIcons = () => {
            if (document.documentElement.classList.contains('dark')) {
                themeToggleDarkIcon.classList.add('hidden');
                themeToggleLightIcon.classList.remove('hidden');
            } else {
                themeToggleDarkIcon.classList.remove('hidden');
                themeToggleLightIcon.classList.add('hidden');
            }
        };

        // Set the initial state of the icons when the page loads
        updateIcons();

        themeToggleBtn.addEventListener('click', () => {
            // Toggle the 'dark' class on the root <html> element
            document.documentElement.classList.toggle('dark');

            // Update localStorage with the new theme preference
            if (document.documentElement.classList.contains('dark')) {
                localStorage.setItem('color-theme', 'dark');
            } else {
                localStorage.setItem('color-theme', 'light');
            }

            // Update the icons to reflect the new state
            updateIcons();

            // Dispatch a custom event that the chart can listen for
            window.dispatchEvent(new CustomEvent('theme-changed'));
        });
    </script>
    <livewire:components.alert />
    <livewire:components.form-modal />
    <livewire:components.confirmation-modal />
    <livewire:components.loading-indicator /> 

    @livewireScripts {{-- <-- ADD THIS LINE --}}
</body>

</html>
