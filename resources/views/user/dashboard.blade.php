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
        <p class="ud-banner__text">Kelola dan temukan dokumen dan sampel arsip PT Sucofindo Cabang Cilacap dengan mudah.</p>

        <form method="GET" action="{{ $urlCari }}" class="ud-search" role="search">
            <button type="submit" class="ud-search__button" aria-label="Cari">
                <x-user.icon name="search" :size="20" />
            </button>
            <input type="search" name="q" class="ud-search__input" placeholder="Cari dokumen atau sampel (nomor, judul, kata kunci).." aria-label="Cari dokumen atau sampel">
        </form>
    </section>

    <section class="ud-shortcuts" aria-label="Pintasan">
        <a href="{{ $urlCari }}" class="ud-shortcut">
            <span class="ud-shortcut__icon"><x-user.icon name="search" :size="24" /></span>
            <span class="ud-shortcut__body">
                <span class="ud-shortcut__title">Cari Arsip</span>
                <span class="ud-shortcut__text">Temukan dokumen atau sampel berdasarkan nomor, judul, atau kata kunci.</span>
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

    <section class="ud-stats ud-stats--arsip" aria-label="Ringkasan arsip">
        <x-user.stat-card label="Total Arsip" :value="$statistik['total']" hint="Dokumen + sampel yang saya unggah" icon="folder" :utama="true" />
        <x-user.stat-card label="Total Dokumen" :value="$statistik['dokumen']" hint="Dokumen yang saya unggah" icon="file" />
        <x-user.stat-card label="Total Sampel" :value="$statistik['sampel']" hint="Sampel yang saya unggah" icon="box" />
    </section>

    <section class="ud-panel ud-panel--terbaru" aria-labelledby="judul-terbaru">
        <h2 class="ud-panel__title" id="judul-terbaru">Arsip Terbaru</h2>
        <div class="ud-list">
            @forelse ($arsipTerbaru as $item)
                @if ($item['tipe'] === 'sampel')
                    <x-user.doc-item
                        :href="route('user.sampel.show', $item['id'])"
                        :title="$item['judul']"
                        :meta="$item['keterangan']"
                        :badge="$item['layanan']"
                        jenis="Sampel"
                        icon="box" />
                @else
                    <x-user.doc-item
                        :href="route('user.dokumen-saya.show', $item['id'])"
                        :title="$item['judul']"
                        :meta="$item['keterangan']"
                        :badge="$item['layanan']"
                        jenis="Dokumen"
                        icon="file" />
                @endif
            @empty
                <p class="ud-empty">Belum ada arsip. Unggah dokumen atau catat sampel untuk mulai mengisi arsip Anda.</p>
            @endforelse
        </div>
    </section>
</x-layouts.user>