<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="manifest" href="/site.webmanifest">

        <link rel="dns-prefetch" href="https://fonts.bunny.net">
        <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
        <link rel="preload" href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600&display=swap" as="style">
        <link rel="preload" href="https://fonts.bunny.net/files/instrument-sans-latin-400-normal.woff2" as="font" type="font/woff2" crossorigin>
        <link rel="preload" href="https://fonts.bunny.net/files/instrument-sans-latin-500-normal.woff2" as="font" type="font/woff2" crossorigin>
        <link rel="preload" href="https://fonts.bunny.net/files/instrument-sans-latin-600-normal.woff2" as="font" type="font/woff2" crossorigin>
        <link rel="stylesheet" href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600&display=swap" media="print" onload="this.media='all'">
        <noscript>
            <link rel="stylesheet" href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600&display=swap" />
        </noscript>

        @viteReactRefresh
        @vite(['resources/js/app.tsx', "resources/js/pages/{$page['component']}.tsx"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
