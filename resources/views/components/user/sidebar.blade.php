@php
    $menu = [
        ['label' => 'Dashboard', 'icon' => 'dashboard', 'route' => 'user.dashboard', 'active' => 'user.dashboard'],
        ['label' => 'Cari Arsip', 'icon' => 'search', 'route' => 'user.arsip.index', 'active' => 'user.arsip.*'],
        ['label' => 'Dokumen Saya', 'icon' => 'file', 'route' => 'user.dokumen-saya.index', 'active' => 'user.dokumen-saya.*'],
        ['label' => 'Sampel Saya', 'icon' => 'box', 'route' => 'user.sampel.index', 'active' => 'user.sampel.*'],
        ['label' => 'Pemusnahan', 'svg' => '<polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>', 'route' => 'pemusnahan.index', 'active' => 'pemusnahan.*'],
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
                
                {{-- Cek apakah pakai komponen icon atau SVG mentah --}}
                @if (isset($item['icon']))
                    <x-user.icon :name="$item['icon']" :size="20" />
                @elseif (isset($item['svg']))
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        {!! $item['svg'] !!}
                    </svg>
                @endif
    
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>
</aside>