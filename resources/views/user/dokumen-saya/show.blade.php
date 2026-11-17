@push('styles')
    <link rel="stylesheet" href="{{ asset('css/user/dokumen-saya.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user/arsip.css') }}">
@endpush

<x-layouts.user title="Detail Dokumen Saya">
    <div class="ua-page-header dk-head">
        <div class="ar-detail-title">
            <div class="ar-resmi-badge">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                Arsip Resmi
            </div>
            <h1 class="ua-page-title">Detail Dokumen Saya</h1>
            <p class="ua-page-subtitle">{{ $dokumen['nomor'] }} &mdash; dokumen yang kamu unggah</p>
        </div>
        <a href="{{ $kembali }}" class="ua-btn ua-btn--outline">Kembali</a>
    </div>

    <div class="ar-detail">
        <div class="ar-detail__kiri">
            <section class="ua-card">
                <h2 class="dk-section-title">Label Box Arsip</h2>
                <x-user.label-box :arsip="$dokumen" />

                @if ($dokumen['lokasi_terisi'])
                    <div class="dk-actions">
                        <a href="{{ route('user.dokumen-saya.label-box', $dokumen['id']) }}" class="ua-btn ua-btn--primary">Download Label Box</a>
                    </div>
                @elseif (filled($dokumen['lokasi_lama']))
                    <p class="dk-note">Catatan lokasi: {{ $dokumen['lokasi_lama'] }}</p>
                @else
                    <p class="dk-note">Lokasi box fisik belum dicatat.</p>
                @endif
            </section>

            <section class="ua-card">
                <h2 class="dk-section-title">Informasi Dokumen</h2>
                <dl class="dk-info">
                    <div class="dk-info__item">
                        <dt>Judul Dokumen</dt>
                        <dd>{{ $dokumen['judul'] }}</dd>
                    </div>
                    <div class="dk-info__item">
                        <dt>Nomor Dokumen</dt>
                        <dd>{{ $dokumen['nomor'] }}</dd>
                    </div>
                    <div class="dk-info__item">
                        <dt>Bagian</dt>
                        <dd>{{ filled($dokumen['portofolio']) ? $dokumen['portofolio'] : '-' }}</dd>
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
                        <dt>Isi / Deskripsi</dt>
                        <dd>{{ filled($dokumen['deskripsi']) ? $dokumen['deskripsi'] : '-' }}</dd>
                    </div>
                </dl>
            </section>
        </div>

        <section class="ua-card ar-berkas" aria-labelledby="judul-berkas">
            <h2 class="dk-section-title" id="judul-berkas">Berkas Dokumen</h2>

            <div class="ar-berkas__info">
                <strong class="ar-berkas__nama">{{ $dokumen['berkas'] }}</strong>
                <span class="ar-berkas__meta">{{ $dokumen['tipe'] }} · {{ $dokumen['ukuran'] }}</span>
            </div>

            @if ($dokumen['berkas_ada'])
                <a href="{{ route('user.arsip.unduh', $dokumen['id']) }}" class="ua-btn ua-btn--primary ar-berkas__unduh">Unduh Dokumen</a>
            @else
                <p class="ar-notice">Berkas digital belum tersedia di server. Hubungi admin bila dokumen ini seharusnya dapat diunduh.</p>
            @endif

            @if ($dokumen['bisa_pratinjau'])
                <div class="ar-preview">
                    @if ($dokumen['pratinjau_gambar'])
                        <img src="{{ route('user.arsip.pratinjau', $dokumen['id']) }}" alt="Pratinjau {{ $dokumen['judul'] }}" class="ar-preview__img">
                    @else
                        <iframe src="{{ route('user.arsip.pratinjau', $dokumen['id']) }}" title="Pratinjau {{ $dokumen['judul'] }}" class="ar-preview__frame"></iframe>
                    @endif
                </div>
            @elseif ($dokumen['berkas_ada'])
                <p class="ar-notice">Format {{ $dokumen['tipe'] }} tidak mendukung pratinjau langsung. Silakan unduh dokumen untuk membukanya.</p>
            @endif
        </section>
    </div>
</x-layouts.user>d