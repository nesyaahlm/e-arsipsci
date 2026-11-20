@push('styles')
    <link rel="stylesheet" href="{{ asset('css/user/dokumen-saya.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user/arsip.css') }}">
    <style>
        .dk-info.sp-info {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px 28px;
        }

        .sp-info .sp-info__full {
            grid-column: 1 / -1;
        }

        @media (max-width: 575.98px) {
            .dk-info.sp-info {
                grid-template-columns: minmax(0, 1fr);
            }
        }
    </style>
@endpush

@php
    $tanggal = fn ($nilai) => $nilai?->copy()->locale('id')->translatedFormat('d F Y') ?? '-';
    $isi = fn ($nilai) => filled($nilai) ? $nilai : '-';

    $lokasi = [
        'Nomor Rak' => $sampel->nomor_ruang_arsip,
        'No. Tingkat' => $sampel->nomor_tingkat_arsip,
        'Nama Ruang' => $sampel->nama_ruang_arsip,
    ];

    $baris = [
        'Portofolio' => $sampel->portofolio?->nama ?? $sampel->bagian?->nama,
        'Tahun' => $sampel->label_tahun,
    ];

    $tipeFoto = strtoupper(pathinfo((string) $sampel->foto_path, PATHINFO_EXTENSION)) ?: '-';
@endphp

<x-layouts.user title="Detail Sampel">
    <div class="ua-page-header dk-head">
        <div class="ar-detail-title">
            <div class="ar-resmi-badge">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                Arsip Sampel
            </div>
            <h1 class="ua-page-title">Detail Sampel</h1>
            <p class="ua-page-subtitle">{{ $sampel->kode_sampel }} &mdash; sampel yang kamu unggah</p>
        </div>
        <a href="{{ $kembali }}" class="ua-btn ua-btn--outline">Kembali</a>
    </div>

    <div class="ar-detail">
        <div class="ar-detail__kiri">
            <section class="ua-card">
                <h2 class="dk-section-title">Label Sampel Arsip</h2>

                <div class="dk-label">
                    <div class="dk-label__top">
                        @foreach ($lokasi as $nama => $nilai)
                            <div class="dk-cell">
                                <span class="dk-cell__name">{{ $nama }}</span>
                                <strong class="dk-cell__value {{ filled($nilai) ? '' : 'is-empty' }}" @if (mb_strlen((string) $nilai) > 7) style="font-size: 17px; overflow-wrap: anywhere;" @endif>{{ $isi($nilai) }}</strong>
                            </div>
                        @endforeach
                    </div>

                    @foreach ($baris as $nama => $nilai)
                        <div class="dk-label__row">
                            <span class="dk-label__name">{{ $nama }}</span>
                            <span class="dk-label__value {{ filled($nilai) ? '' : 'is-empty' }}">{{ $isi($nilai) }}</span>
                        </div>
                    @endforeach

                    <div class="dk-label__row dk-label__row--isi">
                        <span class="dk-label__name">Isi</span>
                        <span class="dk-label__value">
                            <strong class="dk-isi__title">{{ $sampel->nama_sampel }}</strong>
                            <small class="dk-isi__nomor">{{ $sampel->kode_sampel }}</small>
                        </span>
                    </div>
                </div>

                <div class="dk-actions">
                    <a href="{{ route('user.sampel.label', $sampel->id) }}" class="ua-btn ua-btn--primary">Download Label Sampel</a>
                </div>
            </section>

            <section class="ua-card">
                <h2 class="dk-section-title">Informasi Sampel</h2>
                <dl class="dk-info sp-info">
                    <div class="dk-info__item">
                        <dt>Kode Sampel</dt>
                        <dd>{{ $sampel->kode_sampel }}</dd>
                    </div>
                    <div class="dk-info__item">
                        <dt>Nama Sampel</dt>
                        <dd>{{ $sampel->nama_sampel }}</dd>
                    </div>
                    <div class="dk-info__item">
                        
                        {{-- <dt>Bagian</dt>
                        <dd>{{ $sampel->bagian?->nama ?? '-' }}</dd>
                    </div>
                    <div class="dk-info__item">
                        <dt>Tujuan Analisis</dt>
                        <dd>{{ $sampel->analysis_for }}</dd> --}}

                        <dt>Bagian</dt>
                        <dd>{{ $isi($sampel->bagian?->nama) }}</dd>
                    </div>
                    <div class="dk-info__item">
                        <dt>Shipper</dt>
                        <dd>{{ $isi($sampel->shipper) }}</dd>
                    </div>
                    <div class="dk-info__item">
                        <dt>Vessel</dt>
                        <dd>{{ $isi($sampel->vessel) }}</dd>
                    </div>
                    <div class="dk-info__item">
                        <dt>Bill of Lading</dt>
                        <dd>{{ $isi($sampel->bill_of_lading) }}</dd>
                    </div>
                    <div class="dk-info__item">
                        <dt>Tanggal Pengambilan</dt>
                        <dd>{{ $tanggal($sampel->tanggal_pengambilan) }}</dd>
                    </div>
                    <div class="dk-info__item">
                        <dt>Tempat Pengambilan</dt>
                        <dd>{{ $isi($sampel->tempat_pengambilan) }}</dd>
                    </div>
                    <div class="dk-info__item sp-info__full">
                        <dt>Tujuan Analisis</dt>
                        <dd>{{ $isi($sampel->analysis_for) }}</dd>
                    </div>
                    <div class="dk-info__item">
                        <dt>Tanggal Masuk Arsip</dt>
                        <dd>{{ $tanggal($sampel->tanggal_masuk_arsip) }}</dd>
                    </div>
                    <div class="dk-info__item">
                        <dt>Tanggal Diunggah</dt>
                        <dd>{{ $tanggal($sampel->created_at->copy()->setTimezone('Asia/Jakarta')) }}</dd>
                    </div>
                    <div class="dk-info__item sp-info__full">
                        <dt>Isi / Deskripsi</dt>
                        <dd>{!! filled($sampel->deskripsi) ? nl2br(e($sampel->deskripsi)) : '-' !!}</dd>
                    </div>
                </dl>
            </section>
        </div>

        <section class="ua-card ar-berkas" aria-labelledby="judul-foto">
            <h2 class="dk-section-title" id="judul-foto">Foto Sampel</h2>

            @if ($fotoAda)
                <div class="ar-berkas__info">
                    <strong class="ar-berkas__nama">{{ $sampel->nama_sampel }}</strong>
                    <span class="ar-berkas__meta">{{ $tipeFoto }}</span>
                </div>

                <div class="ar-preview">
                    <a href="{{ route('user.sampel.foto', $sampel->id) }}" target="_blank" rel="noopener" title="Klik untuk memperbesar">
                        <img src="{{ route('user.sampel.foto', $sampel->id) }}" alt="Foto {{ $sampel->nama_sampel }}" class="ar-preview__img">
                    </a>
                </div>

                <a href="{{ route('user.sampel.foto', ['sampel' => $sampel->id, 'unduh' => 1]) }}" class="ua-btn ua-btn--primary ar-berkas__unduh" style="margin: 14px 0 0;">Unduh Foto</a>
            @elseif (filled($sampel->foto_path))
                <p class="ar-notice">Foto belum tersedia di server. Hubungi admin bila foto ini seharusnya dapat dilihat.</p>
            @else
                <p class="ar-notice">Belum ada foto untuk sampel ini.</p>
            @endif
        </section>
    </div>
</x-layouts.user>