@push('styles')
    <link rel="stylesheet" href="{{ asset('css/user/dokumen-saya.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user/arsip.css') }}">
@endpush

<x-layouts.user title="Detail Arsip">
    <div class="ua-page-header dk-head">
        <div class="ar-detail-title">
            <div class="ar-resmi-badge">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                Arsip Resmi
            </div>
            <h1 class="ua-page-title">Detail Arsip</h1>
            <p class="ua-page-subtitle">{{ $arsip['nomor'] }} &mdash; dokumen arsip</p>
        </div>
        <a href="{{ $kembali }}" class="ua-btn ua-btn--outline">Kembali</a>
    </div>

    <div class="ar-detail">
        <div class="ar-detail__kiri">
            <section class="ua-card">
                <h2 class="dk-section-title">Label Box Arsip</h2>
                <x-user.label-box :arsip="$arsip" />

                @if (! $arsip['lokasi_terisi'] && filled($arsip['lokasi_lama']))
                    <p class="dk-note">Catatan lokasi: {{ $arsip['lokasi_lama'] }}</p>
                @elseif (! $arsip['lokasi_terisi'])
                    <p class="dk-note">Lokasi box fisik belum dicatat oleh admin.</p>
                @endif
            </section>

            <section class="ua-card">
                <h2 class="dk-section-title">Informasi Dokumen</h2>
                <dl class="dk-info">
                    <div class="dk-info__item">
                        <dt>Judul Dokumen</dt>
                        <dd>{{ $arsip['judul'] }}</dd>
                    </div>
                    <div class="dk-info__item">
                        <dt>Nomor Dokumen</dt>
                        <dd>{{ $arsip['nomor'] }}</dd>
                    </div>
                    <div class="dk-info__item">
                        <dt>Jenis Dokumen</dt>
                        <dd>{{ $arsip['jenis'] }}</dd>
                    </div>
                    <div class="dk-info__item">
                        <dt>Layanan</dt>
                        <dd>{{ $arsip['layanan'] }}</dd>
                    </div>
                    <div class="dk-info__item">
                        <dt>Tanggal Dokumen</dt>
                        <dd>{{ $arsip['tanggal'] }}</dd>
                    </div>
                    <div class="dk-info__item">
                        <dt>Tanggal Diunggah</dt>
                        <dd>{{ $arsip['diunggah'] }}</dd>
                    </div>
                    <div class="dk-info__item">
                        <dt>Masa Retensi</dt>
                        <dd>{{ $arsip['retensi'] ? 'Sampai '.$arsip['retensi'] : 'Selama berlaku' }}</dd>
                    </div>
                    <div class="dk-info__item">
                        <dt>Isi / Deskripsi</dt>
                        <dd>{{ filled($arsip['deskripsi']) ? $arsip['deskripsi'] : '-' }}</dd>
                    </div>
                </dl>
            </section>
        </div>

        <section class="ua-card ar-berkas" aria-labelledby="judul-berkas">
            <h2 class="dk-section-title" id="judul-berkas">Berkas Dokumen</h2>

            <div class="ar-berkas__info">
                <strong class="ar-berkas__nama">{{ $arsip['berkas'] }}</strong>
                <span class="ar-berkas__meta">{{ $arsip['tipe'] }} · {{ $arsip['ukuran'] }}</span>
            </div>

            @if ($arsip['berkas_ada'])
                <a href="{{ route('user.arsip.unduh', $arsip['id']) }}" class="ua-btn ua-btn--primary ar-berkas__unduh">Unduh Dokumen</a>
            @else
                <p class="ar-notice">Berkas digital belum tersedia di server. Hubungi admin bila dokumen ini seharusnya dapat diunduh.</p>
            @endif

            @if ($arsip['bisa_pratinjau'])
                <div class="ar-preview">
                    @if ($arsip['pratinjau_gambar'])
                        <img src="{{ route('user.arsip.pratinjau', $arsip['id']) }}" alt="Pratinjau {{ $arsip['judul'] }}" class="ar-preview__img">
                    @else
                        <iframe src="{{ route('user.arsip.pratinjau', $arsip['id']) }}" title="Pratinjau {{ $arsip['judul'] }}" class="ar-preview__frame"></iframe>
                    @endif
                </div>
            @elseif ($arsip['berkas_ada'])
                <p class="ar-notice">Format {{ $arsip['tipe'] }} tidak mendukung pratinjau langsung. Silakan unduh dokumen untuk membukanya.</p>
            @endif
        </section>
    </div>
</x-layouts.user>