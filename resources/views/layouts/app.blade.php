<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('judul') - E-Arsip</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/sucofindo-nobg.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin/app.css') }}">
    @stack('css')
</head>
<body class="bg-white">
<div class="d-flex min-vh-100">
    <aside class="sidebar p-3">
        <div class="brand px-2 pb-3 mb-2">
            <div class="fs-5 fw-bold">PT SUCOFINDO</div>
            <div class="small opacity-75">Kearsipan Dokumen</div>
        </div>
        <nav class="nav nav-pills flex-column gap-1">
            {{-- <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('user.dokumen.index') }}" class="nav-link d-flex align-items-center gap-2"> --}}
                <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('user.dashboard') }}" class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('admin.dashboard', 'user.dashboard') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('dokumen.index') }}" class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('dokumen.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/></svg>
                <span>Dokumen</span>
            </a>
            <a href="{{ route('layanan.index') }}" class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('layanan.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><path d="M12 3l9 5-9 5-9-5z"/><path d="M3 13l9 5 9-5"/></svg>
                <span>Layanan &amp; Jenis</span>
            </a>
            @if (auth()->user()->isAdmin())
                <a href="#" class="nav-link d-flex align-items-center gap-2">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
                    <span>Kelola User</span>
                </a>
                <a href="#" class="nav-link d-flex align-items-center gap-2">
                    <svg viewBox="0 0 24 24"><path d="M5 4h14v16H5z"/><path d="M8 9h8M8 13h8M8 17h5"/></svg>
                    <span>Log Aktivitas</span>
                </a>
            @endif
        </nav>
    </aside>

    <div class="konten-utama flex-grow-1">
        <header class="topbar px-3 px-md-4 py-3 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <button type="button" class="btn-hamburger" id="toggle-sidebar" aria-label="Buka atau tutup menu">
                    <svg viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
                </button>
                <div>
                    <div class="fs-5 fw-bold lh-1">E-ARSIP</div>
                    <div class="small fst-italic">Sistem Elektronik Arsip</div>
                </div>
            </div>

            <div class="dropdown">
                <button type="button" class="btn-profil d-flex align-items-center gap-2" data-bs-toggle="dropdown" aria-expanded="false">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="10" r="3"/><path d="M6.5 18.5c1.2-2.2 3-3.2 5.5-3.2s4.3 1 5.5 3.2"/></svg>
                    <span class="text-start lh-sm">
                        <span class="d-block fw-bold">{{ auth()->user()->name }}</span>
                        <span class="d-block small">{{ ucfirst(auth()->user()->role) }}</span>
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">Keluar</button>
                        </form>
                    </li>
                </ul>
            </div>
        </header>

        <main class="p-3 p-md-4">
            @yield('konten')
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('toggle-sidebar').addEventListener('click', function () {
        const layarKecil = window.matchMedia('(max-width: 767.98px)').matches;
        document.body.classList.toggle(layarKecil ? 'sidebar-buka' : 'sidebar-tutup');
    });
</script>
@stack('skrip')
</body>
</html>