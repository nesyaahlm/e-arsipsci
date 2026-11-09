@php
    $menu = [
        ['label' => 'Dashboard', 'icon' => 'dashboard', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard'],
        ['label' => 'Dokumen', 'icon' => 'file', 'route' => '#', 'active' => '-'],
        ['label' => 'Layanan & Jenis', 'icon' => 'upload', 'route' => 'layanan.index', 'active' => 'layanan.*'],
        ['label' => 'Kelola User', 'icon' => 'user', 'route' => '#', 'active' => '-'],
        ['label' => 'Log Aktivitas', 'icon' => 'user', 'route' => '#', 'active' => '-'],
    ];
@endphp
<aside class="ua-sidebar" id="ua-sidebar" aria-label="Navigasi admin">
    <div class="ua-sidebar__brand">
        <span class="ua-brand__name">PT SUCOFINDO</span>
        <span class="ua-brand__sub">Kerasipan Dokumen</span>
    </div>

    <nav class="ua-nav">
        @foreach ($menu as $item)
            @php $aktif = request()->routeIs($item['active']); @endphp
            <a href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}"
               class="ua-nav__link {{ $aktif ? 'is-active' : '' }}"
               @if ($aktif) aria-current="page" @endif>
                <x-user.icon :name="$item['icon']" :size="20" />
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>
</aside>