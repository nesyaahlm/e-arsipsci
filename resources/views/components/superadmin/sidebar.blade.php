@php
    $menu = [
        ['label' => 'Dashboard', 'svg' => '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>', 'route' => 'superadmin.dashboard', 'active' => 'superadmin.dashboard'],
        ['label' => 'Kode Surat', 'svg' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>', 'route' => 'superadmin.kode-surat.index', 'active' => 'superadmin.kode-surat.*'],
        ['label' => 'Bagian / Divisi', 'svg' => '<rect x="3" y="3" width="18" height="6" rx="1"/><rect x="3" y="13" width="18" height="8" rx="1"/>', 'route' => 'superadmin.bagian.index', 'active' => 'superadmin.bagian.*'],
        ['label' => 'Portofolio', 'svg' => '<path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>', 'route' => 'superadmin.portofolio.index', 'active' => 'superadmin.portofolio.*'],
        ['label' => 'Arsip Dokumen', 'svg' => '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/>', 'route' => 'superadmin.dokumen.index', 'active' => 'superadmin.dokumen.*'],
        ['label' => 'Arsip Sampel', 'svg' => '<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/>', 'route' => 'superadmin.sampel.index', 'active' => 'superadmin.sampel.*'],
        ['label' => 'Kelola Admin Unit Kerja', 'svg' => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/>', 'route' => 'superadmin.admin-cabang.index', 'active' => 'superadmin.admin-cabang.*'],
        ['label' => 'Log Aktivitas', 'svg' => '<path d="M12 8v4l3 3"/><circle cx="12" cy="12" r="9"/>', 'route' => 'superadmin.log-aktivitas.index', 'active' => 'superadmin.log-aktivitas.*'],
    ];
@endphp
<aside class="ua-sidebar" id="ua-sidebar" aria-label="Navigasi superadmin">
    <div class="ua-sidebar__brand">
        <span class="ua-brand__name">PT SUCOFINDO</span>
        <span class="ua-brand__sub">Kantor Pusat (Holding)</span>
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