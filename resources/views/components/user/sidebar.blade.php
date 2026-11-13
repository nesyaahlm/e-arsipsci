@php
    $menu = [
        ['label' => 'Dashboard', 'icon' => 'dashboard', 'route' => 'user.dashboard', 'active' => 'user.dashboard'],
        ['label' => 'Cari Arsip', 'icon' => 'search', 'route' => 'user.arsip.index', 'active' => 'user.arsip.*'],
        ['label' => 'Dokumen Saya', 'icon' => 'file', 'route' => 'user.dokumen-saya.index', 'active' => 'user.dokumen-saya.*'],
        // ['label' => 'Profile', 'icon' => 'user', 'route' => 'profile.edit', 'active' => 'profile.*'],
    ];
@endphp
<aside class="ua-sidebar" id="ua-sidebar" aria-label="Navigasi pengguna">
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
                <x-user.icon :name="$item['icon']" :size="20" />
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>
</aside>