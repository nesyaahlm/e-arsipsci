<header class="ua-topbar">
    <div class="ua-topbar__brand">
        <span class="ua-brand__name">PT SUCOFINDO</span>
        <span class="ua-brand__sub">Kearsipan Dokumen</span>
    </div>

    <button type="button" class="ua-topbar__toggle" data-ua-toggle aria-controls="ua-sidebar" aria-expanded="false" aria-label="Buka atau tutup menu">
        <x-user.icon name="menu" :size="24" />
    </button>

    <div class="ua-topbar__title">
        <span class="ua-topbar__app">E-ARSIP</span>
        <span class="ua-topbar__tagline">Sistem Elektronik Arsip</span>
    </div>

    <div class="ua-user" data-ua-user>
        <button type="button" class="ua-user__button" data-ua-user-toggle aria-haspopup="true" aria-expanded="false">
            <span class="ua-user__avatar">
                @if ($fotoUrl)
                    <img src="{{ $fotoUrl }}" alt="Foto {{ $nama }}">
                @else
                    <x-user.icon name="avatar" :size="42" :stroke="1.5" />
                @endif
            </span>
            <span class="ua-user__meta">
                <span class="ua-user__name">{{ $nama }}</span>
                <span class="ua-user__role">{{ $peran }}</span>
            </span>
            <x-user.icon name="chevron-down" :size="16" />
        </button>

        <div class="ua-user__menu" data-ua-user-menu hidden>
            <a href="{{ Route::has('profile.index') ? route('profile.index') : '#' }}">
                <x-user.icon name="user" :size="18" />
                Profile
            </a>
            <form method="POST" action="{{ Route::has('logout') ? route('logout') : '#' }}">
                @csrf
                <button type="submit">
                    <x-user.icon name="logout" :size="18" />
                    Keluar
                </button>
            </form>
        </div>
    </div>
</header>