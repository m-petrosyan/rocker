<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#000000">

    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="apple-touch-icon" href="/favicon-apple.png">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @routes
    @vite([
        'resources/js/app.js',
        "resources/js/Pages/{$page['component']}.vue"
    ])
    @inertiaHead
</head>
<body class="font-sans antialiased bg-gray-900 text-white">
@inertia
</body>
</html>
