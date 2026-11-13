@push('styles')
    <link rel="stylesheet" href="{{ asset('css/user/dokumen-saya.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user/arsip.css') }}">
@endpush

<x-layouts.user title="Detail Sampel">
    <div class="ua-page-header dk-head">
        <div>
            <h1 class="ua-page-title">Detail Sampel</h1>
            <p class="ua-page-subtitle">{{ $sampel->kode_sampel }} &mdash; sampel yang kamu unggah</p>
        </div>
        <a href="{{ route('user.sampel.index') }}" class="ua-btn ua-btn--outline">Kembali</a>
    </div>

    <div class="ar-detail">
        <div class="ar-detail__kiri">
            <section class="ua-card">
                <h2 class="dk-section-title">Informasi Sampel</h2>
                <dl class="dk-info">
                    <div class="dk-info__item">
                        <dt>Kode Sampel</dt>
                        <dd>{{ $sampel->kode_sampel }}</dd>
                    </div>
                    <div class="dk-info__item">
                        <dt>Nama Sampel</dt>
                        <dd>{{ $sampel->nama_sampel }}</dd>
                    </div>
                    <div class="dk-info__item">
                        <dt>Layanan</dt>
                        <dd>{{ $sampel->layanan?->nama ?? '-' }}</dd>
                    </div>
                    <div class="dk-info__item">
                        <dt>Tujuan Analisis</dt>
                        <dd>{{ $sampel->analysis_for }}</dd>
                    </div>
                    <div class="dk-info__item">
                        <dt>Shipper</dt>
                        <dd>{{ filled($sampel->shipper) ? $sampel->shipper : '-' }}</dd>
                    </div>
                    <div class="dk-info__item">
                        <dt>Vessel</dt>
                        <dd>{{ filled($sampel->vessel) ? $sampel->vessel : '-' }}</dd>
                    </div>
                    <div class="dk-info__item">
                        <dt>Bill of Lading</dt>
                        <dd>{{ filled($sampel->bill_of_lading) ? $sampel->bill_of_lading : '-' }}</dd>
                    </div>
                    <div class="dk-info__item">
                        <dt>Tanggal Pengambilan</dt>
                        <dd>{{ $sampel->tanggal_pengambilan->locale('id')->translatedFormat('d F Y') }}</dd>
                    </div>
                    <div class="dk-info__item">
                        <dt>Tempat Pengambilan</dt>
                        <dd>{{ $sampel->tempat_pengambilan }}</dd>
                    </div>
                    <div class="dk-info__item">
                        <dt>Deskripsi</dt>
                        <dd>{!! filled($sampel->deskripsi) ? nl2br(e($sampel->deskripsi)) : '-' !!}</dd>
                    </div>
                </dl>
            </section>

            <section class="ua-card">
                <h2 class="dk-section-title">Lokasi Arsip Sampel</h2>
                <dl class="dk-info">
                    <div class="dk-info__item">
                        <dt>Nomor Ruang / Rak</dt>
                        <dd>{{ $sampel->nomor_ruang_arsip }}</dd>
                    </div>
                    <div class="dk-info__item">
                        <dt>Nama Ruang Arsip</dt>
                        <dd>{{ $sampel->nama_ruang_arsip }}</dd>
                    </div>
                    <div class="dk-info__item">
                        <dt>Tanggal Masuk Arsip</dt>
                        <dd>{{ $sampel->tanggal_masuk_arsip->locale('id')->translatedFormat('d F Y') }}</dd>
                    </div>
                    <div class="dk-info__item">
                        <dt>Tanggal Diunggah</dt>
                        <dd>{{ $sampel->created_at->copy()->setTimezone('Asia/Jakarta')->locale('id')->translatedFormat('d F Y') }}</dd>
                    </div>
                </dl>
            </section>
        </div>

        <section class="ua-card ar-berkas" aria-labelledby="judul-foto">
            <h2 class="dk-section-title" id="judul-foto">Foto Sampel</h2>

            @if ($fotoAda)
                <div class="ar-preview">
                    <img src="{{ route('user.sampel.foto', $sampel->id) }}" alt="Foto {{ $sampel->nama_sampel }}" class="ar-preview__img">
                </div>
            @elseif (filled($sampel->foto_path))
                <p class="ar-notice">Berkas foto belum tersedia di server. Hubungi admin bila foto ini seharusnya ada.</p>
            @else
                <p class="ar-notice">Tidak ada foto yang diunggah untuk sampel ini.</p>
            @endif
        </section>
    </div>
</x-layouts.user>