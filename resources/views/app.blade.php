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
    <link rel="apple-touch-icon" href="/favicon-apple.png">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.png') }}" />
    @if(request()->has('page') && request()->get('page') > 1)
        <link rel="canonical" href="{{ url()->current() }}" />
    @else
        <link rel="canonical" href="{{ request()->url() }}" />
    @endif
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
<body class="font-sans antialiased bg-gray-900 text-white">
@inertia
</body>
</html>
