<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#173b5b">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <link rel="icon" href="{{ asset('icon.svg') }}" type="image/svg+xml">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    boxShadow: {
                        app: '0 18px 60px rgba(15, 23, 42, 0.18)',
                        card: '0 8px 28px rgba(15, 23, 42, 0.08)',
                    },
                },
            },
        };
    </script>
    <style>
        html { -webkit-tap-highlight-color: transparent; }
        body { overscroll-behavior-y: none; }
        .safe-top { padding-top: max(1rem, env(safe-area-inset-top)); }
        .safe-bottom { padding-bottom: max(.75rem, env(safe-area-inset-bottom)); }
    </style>
    <title>@yield('title', 'Check In Tamu Digital')</title>
</head>
<body class="min-h-screen bg-slate-200 font-sans text-slate-800 antialiased">
    <div class="relative mx-auto min-h-screen w-full max-w-xl overflow-x-hidden bg-[#f4f6f8] shadow-app">
        @yield('content')
    </div>

    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('{{ asset('service-worker.js') }}');
        }
    </script>
    @stack('scripts')
</body>
</html>

