<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#000000">
    <meta name="publisher" content="Rocker.am">
    <meta name="theme-color" content="#000000">
    <meta name="apple-mobile-web-app-title" content="rocker.am">
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    <link rel="icon" type="image/png" href="/favicon.png">
    <meta name="robots" content="index, follow">
    <script src="https://telegram.org/js/telegram-web-app.js"></script>
    <script type="application/ld+json">
        @verbatim
            {
              "@context": "https://schema.org",
          "@type": "WebSite",
          "url": "{{ url('/') }}",
          "name": "Rocker"
        }
        @endverbatim
    </script>
    @routes
    @vite([
        'resources/js/app.js',
        "resources/js/Pages/{$page['component']}.vue"
    ])
    @inertiaHead
</head>
<body class="min-h-max font-sans antialiased bg-gray-900 text-white">
@inertia
</body>
</html>
