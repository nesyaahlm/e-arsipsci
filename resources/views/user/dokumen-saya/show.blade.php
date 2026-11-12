@push('styles')
    <link rel="stylesheet" href="{{ asset('css/user/dokumen-saya.css') }}">
@endpush

<x-layouts.user title="Detail Dokumen Saya">

    <div class="ua-page-header dk-head">
        <div>
            <h1 class="ua-page-title">Detail Dokumen Saya</h1>
            <p class="ua-page-subtitle">{{ $dokumen['nomor'] }} &mdash; dokumen yang kamu unggah</p>
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

            @if ($dokumen['berkas_ada'])
                <div class="dk-actions">
                    @if ($dokumen['bisa_pratinjau'])
                        <a href="{{ route('user.arsip.pratinjau', $dokumen['id']) }}" target="_blank" rel="noopener" class="ua-btn ua-btn--outline">Buka Dokumen</a>
                    @endif
                    <a href="{{ route('user.arsip.unduh', $dokumen['id']) }}" class="ua-btn ua-btn--primary">Unduh Dokumen</a>
                </div>
            @else
                <p class="dk-note">Berkas digital belum tersedia di server. Hubungi admin bila dokumen ini seharusnya dapat diunduh.</p>
            @endif
        </section>
    </div>
</x-layouts.user>