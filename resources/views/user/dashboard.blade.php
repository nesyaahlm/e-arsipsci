@push('styles')
    <link rel="stylesheet" href="{{ asset('css/user/dashboard.css') }}">
@endpush

@php
    $urlCari = Route::has('user.arsip.index') ? route('user.arsip.index') : '#';
    $urlUnggah = Route::has('user.dokumen-saya.index') ? route('user.dokumen-saya.index').'#unggah' : '#';
@endphp

<x-layouts.user title="Dashboard">
    <section class="ud-banner">
        <h1 class="ud-banner__title">Selamat Datang, {{ $user->name }}!</h1>
        <p class="ud-banner__text">Kelola dan temukan dokumen arsip PT Sucofindo Cabang Cilacap dengan mudah.</p>

        <form method="GET" action="{{ $urlCari }}" class="ud-search" role="search">
            <button type="submit" class="ud-search__button" aria-label="Cari">
                <x-user.icon name="search" :size="20" />
            </button>
            <input type="search" name="q" class="ud-search__input" placeholder="Cari dokumen (nomor, judul, kata kunci).." aria-label="Cari dokumen">
        </form>
    </section>

    <section class="ud-shortcuts" aria-label="Pintasan">
        <a href="{{ $urlCari }}" class="ud-shortcut">
            <span class="ud-shortcut__icon"><x-user.icon name="search" :size="24" /></span>
            <span class="ud-shortcut__body">
                <span class="ud-shortcut__title">Cari Arsip</span>
                <span class="ud-shortcut__text">Temukan dokumen berdasarkan nomor, judul, atau kata kunci.</span>
            </span>
        </a>
        <a href="{{ $urlUnggah }}" class="ud-shortcut ud-shortcut--primary">
            <span class="ud-shortcut__icon"><x-user.icon name="upload" :size="24" /></span>
            <span class="ud-shortcut__body">
                <span class="ud-shortcut__title">Unggah Dokumen</span>
                <span class="ud-shortcut__text">Unggah dokumen baru, langsung tersedia di arsip.</span>
            </span>
        </a>
    </section>

    <section class="ud-stats" aria-label="Ringkasan dokumen">
        <x-user.stat-card label="Dokumen Saya" :value="$statistik['saya']" hint="Dokumen yang saya unggah" icon="file" />
        <x-user.stat-card label="Diunggah Bulan Ini" :value="$statistik['bulan_ini']" :hint="$statistik['bulan']" icon="upload" />
        <x-user.stat-card label="Favorit Saya" :value="$statistik['favorit']" hint="Dokumen ditandai favorit" icon="star" />
        <x-user.stat-card label="Total Arsip" :value="$statistik['total']" hint="Semua layanan" icon="folder" />
    </section>

    <div class="ud-columns">
        <section class="ud-panel ud-panel--terbaru" aria-labelledby="judul-terbaru">
            <h2 class="ud-panel__title" id="judul-terbaru">Dokumen Terbaru</h2>
            <div class="ud-list">
                @forelse ($dokumenTerbaru as $item)
                    <x-user.doc-item
                        :href="Route::has('user.arsip.show') ? route('user.arsip.show', $item['id']) : '#'"
                        :title="$item['judul']"
                        :meta="$item['keterangan']"
                        :badge="$item['layanan']"
                        icon="file" />
                @empty
                    <p class="ud-empty">Belum ada dokumen di arsip.</p>
                @endforelse
            </div>
        </section>

        <section class="ud-panel" aria-labelledby="judul-aktivitas">
            <h2 class="ud-panel__title" id="judul-aktivitas">Favorit &amp; Terakhir Dibuka</h2>
            <div class="ud-list">
                @forelse ($aktivitas as $item)
                    <x-user.doc-item
                        :href="Route::has('user.arsip.show') ? route('user.arsip.show', $item['id']) : '#'"
                        :title="$item['judul']"
                        :meta="$item['keterangan']"
                        :icon="$item['ikon']" />
                @empty
                    <p class="ud-empty">Belum ada dokumen favorit atau yang pernah Anda buka.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-layouts.user>   