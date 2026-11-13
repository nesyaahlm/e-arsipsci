@php
    $menu = [
        ['label' => 'Dashboard', 'svg' => '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard'],
        ['label' => 'Dokumen', 'svg' => '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/>', 'route' => 'dokumen.index', 'active' => 'dokumen.*'],
        ['label' => 'Sampel', 'svg' => '<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/>', 'route' => 'admin.sampel.index', 'active' => 'admin.sampel.*'],
        ['label' => 'Layanan & Jenis', 'svg' => '<path d="M12 3l9 5-9 5-9-5z"/><path d="M3 13l9 5 9-5"/>', 'route' => 'layanan.index', 'active' => 'layanan.*'],
        ['label' => 'Kelola User', 'svg' => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/>', 'route' => 'users.index', 'active' => 'users.*'],
        ['label' => 'Log Aktivitas', 'svg' => '<path d="M5 4h14v16H5z"/><path d="M8 9h8M8 13h8M8 17h5"/>', 'route' => 'log.index', 'active' => 'log.*'],
    ];
@endphp
<aside class="ua-sidebar" id="ua-sidebar" aria-label="Navigasi admin">
    <div class="ua-sidebar__brand">
        <span class="ua-brand__name">PT SUCOFINDO</span>
        <span class="ua-brand__sub">Kearsipan Dokumen</span>
    </div>

    <nav class="ua-nav">
        @foreach ($menu as $item)
            @php $aktif = request()->routeIs($item['active']); @endphp
            <a href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}"
               class="ua-nav__link {{ $aktif ? 'is-active' : '' }}"
               @if ($aktif) aria-current="page" @endif>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $item['svg'] !!}</svg>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>
</aside>