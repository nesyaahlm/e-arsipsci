<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('judul') - E-Arsip</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('css')
</head>
<body class="bg-body-tertiary">
<div class="d-flex flex-column flex-md-row min-vh-100">
    <aside class="sidebar p-3">
        <div class="brand pb-3 mb-3 px-2">
            <div class="fs-5 fw-bold">PT SUCOFINDO</div>
            <div class="small opacity-75">Kearsipan Dokumen</div>
        </div>
        <nav class="nav nav-pills flex-column gap-1">
            <a href="#" class="nav-link d-flex align-items-center gap-2">
                <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                <span>Dashboard</span>
            </a>
            <a href="#" class="nav-link d-flex align-items-center gap-2">
                <svg viewBox="0 0 24 24"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/></svg>
                <span>Dokumen</span>
            </a>
            <a href="#" class="nav-link d-flex align-items-center gap-2">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M8 12l3 3 5-6"/></svg>
                <span>Verifikasi</span>
            </a>
            <a href="{{ route('layanan.index') }}" class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('layanan.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><path d="M12 3l9 5-9 5-9-5z"/><path d="M3 13l9 5 9-5"/></svg>
                <span>Layanan &amp; Jenis</span>
            </a>
            <a href="#" class="nav-link d-flex align-items-center gap-2">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
                <span>Kelola User</span>
            </a>
            <a href="#" class="nav-link d-flex align-items-center gap-2">
                <svg viewBox="0 0 24 24"><path d="M5 4h14v16H5z"/><path d="M8 9h8M8 13h8M8 17h5"/></svg>
                <span>Log Aktivitas</span>
            </a>
        </nav>
    </aside>

    <div class="flex-grow-1 min-w-0">
        <header class="bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
            <div>
                <div class="fs-5 fw-bold text-primary">E-ARSIP</div>
                <div class="small text-secondary">Sistem Elektronik Arsip</div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="text-end">
                    <div class="fw-semibold">{{ auth()->user()->name }}</div>
                    <div class="small text-secondary">{{ ucfirst(auth()->user()->role) }}</div>
                </div>
                <div class="avatar rounded-circle bg-primary text-white fw-bold d-flex align-items-center justify-content-center">{{ collect(explode(' ', auth()->user()->name))->take(2)->map(fn ($kata) => mb_strtoupper(mb_substr($kata, 0, 1)))->implode('') }}</div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-secondary">Keluar</button>
                </form>
            </div>
        </header>

        <main class="p-4">
            @yield('konten')
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('skrip')
</body>
</html>