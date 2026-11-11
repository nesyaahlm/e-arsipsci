@props(['title' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title.' | ' : '' }}E-Arsip PT Sucofindo Cilacap</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,400i,500,600,700" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --bs-primary: #0f766e;
            --bs-primary-rgb: 15, 118, 110;
            --bs-body-font-family: 'Inter', system-ui, sans-serif;
        }
    </style>
    <script>
        try {
            if (window.matchMedia('(min-width: 992px)').matches && localStorage.getItem('ua-sidebar-collapsed') === '1') {
                document.documentElement.classList.add('ua-sidebar-collapsed');
            }
        } catch (e) {}
    </script>
    <link rel="stylesheet" href="{{ asset('css/user/layout.css') }}">
    @stack('styles')
</head>
<body class="ua-body">
    <a class="ua-skip" href="#ua-content">Lewati ke konten</a>

    <x-user.navbar />
    <x-user.sidebar />
    <div class="ua-overlay" data-ua-overlay></div>

    <main class="ua-main" id="ua-content">
        {{ $slot }}
    </main>

    @stack('overlays')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.alert-otomatis').forEach(function (el) {
            setTimeout(function () {
                bootstrap.Alert.getOrCreateInstance(el).close();
            }, 4000);
        });
    </script>
    <script src="{{ asset('js/user/layout.js') }}" defer></script>
    @stack('scripts')
</body>
</html>