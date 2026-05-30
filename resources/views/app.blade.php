<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts — self-hosted to avoid render-blocking external request -->
        <link rel="preload" href="/fonts/figtree-400.woff2" as="font" type="font/woff2" crossorigin>
        <link rel="preload" href="/fonts/figtree-500.woff2" as="font" type="font/woff2" crossorigin>
        <link rel="preload" href="/fonts/figtree-600.woff2" as="font" type="font/woff2" crossorigin>

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="/favicon.svg">
        <link rel="icon" type="image/x-icon" href="/favicon.ico">

        <!-- PWA -->
        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#0f172a">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="FieldOps">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <!-- Apply saved theme before first paint to avoid flash -->
        <script>
            (function () {
                var appearance = document.cookie.match(/(?:^|;\s*)appearance=([^;]+)/);
                var value = appearance ? appearance[1] : localStorage.getItem('appearance');
                if (value === 'dark' || (value !== 'light' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                }
            })();
        </script>

        <!-- Scripts -->
        @vite(['resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
