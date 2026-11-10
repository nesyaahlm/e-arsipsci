<x-layouts.user title="Detail Arsip">
    <link rel="stylesheet" href="{{ asset('css/user/dokumen-saya.css') }}">

    <div class="ua-page-header dk-head">
        <div>
            <h1 class="ua-page-title">Detail Arsip</h1>
            <p class="ua-page-subtitle">{{ $dokumen['nomor'] }}</p>
        </div>
        <a href="{{ route('user.dokumen-saya.index') }}" class="ua-btn ua-btn--outline">Kembali</a>
    </div>

    <div class="dk-detail">
        <section class="ua-card">
            <h2 class="dk-section-title">Label Box Arsip</h2>
            <x-user.label-box :arsip="$dokumen" />

            @if (! $dokumen['lokasi_terisi'] && filled($dokumen['lokasi_lama']))
                <p class="dk-note">Catatan lokasi sebelumnya: {{ $dokumen['lokasi_lama'] }}</p>
            @endif
        </section>

        <section class="ua-card">
            <h2 class="dk-section-title">Informasi Dokumen</h2>
            <dl class="dk-info">
                <div class="dk-info__item">
                    <dt>Status</dt>
                    <dd><span class="ua-badge ua-badge--{{ $dokumen['status'] }}">{{ $dokumen['status_label'] }}</span></dd>
                </div>
                @if (filled($dokumen['catatan']))
                    <div class="dk-info__item">
                        <dt>Catatan Admin</dt>
                        <dd>{{ $dokumen['catatan'] }}</dd>
                    </div>
                @endif
                <div class="dk-info__item">
                    <dt>Nomor Dokumen</dt>
                    <dd>{{ $dokumen['nomor'] }}</dd>
                </div>
                <div class="dk-info__item">
                    <dt>Jenis Dokumen</dt>
                    <dd>{{ $dokumen['jenis'] }}</dd>
                </div>
                <div class="dk-info__item">
                    <dt>Tanggal Dokumen</dt>
                    <dd>{{ $dokumen['tanggal'] }}</dd>
                </div>
                <div class="dk-info__item">
                    <dt>Tanggal Diunggah</dt>
                    <dd>{{ $dokumen['diunggah'] }}</dd>
                </div>
                <div class="dk-info__item">
                    <dt>Masa Retensi</dt>
                    <dd>{{ $dokumen['retensi'] ? 'Sampai '.$dokumen['retensi'] : 'Selama berlaku' }}</dd>
                </div>
                <div class="dk-info__item">
                    <dt>Berkas</dt>
                    <dd>{{ filled($dokumen['berkas']) ? $dokumen['berkas'].' ('.$dokumen['ukuran'].')' : $dokumen['ukuran'] }}</dd>
                </div>
                <div class="dk-info__item">
                    <dt>Deskripsi</dt>
                    <dd>{{ filled($dokumen['deskripsi']) ? $dokumen['deskripsi'] : '-' }}</dd>
                </div>
            </dl>
        </section>
    </div>
</x-layouts.user>