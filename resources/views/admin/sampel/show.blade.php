@php
    $infoArsip = [
        ['Kode Sampel', $sampel->kode_sampel, 'fw-bold text-primary font-monospace'],
        ['Nama Sampel', $sampel->nama_sampel, ''],
        ['Layanan', $sampel->layanan->nama ?? '-', ''],
        ['Tgl Masuk Arsip', $sampel->tanggal_masuk_arsip?->format('d/m/Y') ?? '-', ''],
        ['Nomor Ruang', $sampel->nomor_ruang_arsip, ''],
        ['Nama Ruang', $sampel->nama_ruang_arsip, ''],
        ['Diunggah Oleh', $sampel->pengunggah->name ?? '-', ''],
    ];

    $infoSampel = [
        ['Shipper', $sampel->shipper ?: '-', ''],
        ['Vessel', $sampel->vessel ?: '-', ''],
        ['BL', $sampel->bill_of_lading ?: '-', ''],
        ['Tgl Pengambilan', $sampel->tanggal_pengambilan?->format('d/m/Y') ?? '-', ''],
        ['Tempat Pengambilan', $sampel->tempat_pengambilan, ''],
        ['Analysis For', $sampel->analysis_for, ''],
    ];
@endphp

<x-admin.layout title="Detail Sampel">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
        <div>
            <h1 class="h3 fw-bold text-primary mb-1">Detail Sampel</h1>
            <p class="text-secondary mb-0 small">Informasi lengkap sampel arsip.</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('admin.sampel.edit', $sampel->id) }}" class="btn btn-primary btn-sm fw-semibold px-3">Edit</a>
            <a href="{{ route('admin.sampel.index') }}" class="btn btn-outline-primary btn-sm fw-semibold px-3">Kembali</a>
        </div>
    </div>

    <div class="row g-3 align-items-start">
        <div class="col-12 col-md-4 col-lg-3">
            <div class="card shadow-sm rounded-4 overflow-hidden" style="border: 1.5px solid #cfdedb;">
                <div class="ratio ratio-4x3" style="background-color: #eef4f2;">
                    @if ($sampel->foto_path)
                        <a href="{{ asset('storage/'.$sampel->foto_path) }}" target="_blank" rel="noopener">
                            <img src="{{ asset('storage/'.$sampel->foto_path) }}"
                                 alt="Foto {{ $sampel->nama_sampel }}"
                                 class="w-100 h-100" style="object-fit: cover;">
                        </a>
                    @else
                        <div class="d-flex flex-column align-items-center justify-content-center text-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                <path d="M21 15l-5-5L5 21"></path>
                            </svg>
                            <span class="small mt-1">Belum ada foto</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-md-8 col-lg-9">
            <div class="card shadow-sm rounded-4" style="border: 1.5px solid #cfdedb;">
                <div class="card-body p-3 p-lg-4">
                    <div class="row g-4">
                        <div class="col-12 col-xl-6">
                            <h2 class="h6 fw-bold text-primary mb-3">Informasi Arsip</h2>

                            <dl class="row mb-0 small">
                                @foreach ($infoArsip as [$label, $nilai, $kelas])
                                    <dt class="col-5 text-secondary fw-semibold d-flex justify-content-between">
                                        <span>{{ $label }}</span><span>:</span>
                                    </dt>
                                    <dd class="col-7 {{ $kelas }} {{ $loop->last ? 'mb-0' : '' }}">{{ $nilai }}</dd>
                                @endforeach
                            </dl>
                        </div>

                        <div class="col-12 col-xl-6">
                            <h2 class="h6 fw-bold text-primary mb-3">Informasi Sampel</h2>

                            <dl class="row mb-0 small">
                                @foreach ($infoSampel as [$label, $nilai, $kelas])
                                    <dt class="col-5 text-secondary fw-semibold d-flex justify-content-between">
                                        <span>{{ $label }}</span><span>:</span>
                                    </dt>
                                    <dd class="col-7 {{ $kelas }} {{ $loop->last ? 'mb-0' : '' }}">{{ $nilai }}</dd>
                                @endforeach
                            </dl>
                        </div>
                    </div>

                    <hr class="my-3" style="border-color: #cfdedb;">

                    <dl class="row mb-0 small">
                        <dt class="col-5 col-xl-2 text-secondary fw-semibold d-flex justify-content-between">
                            <span>Deskripsi</span><span>:</span>
                        </dt>
                        <dd class="col-7 col-xl-10 mb-0">{{ $sampel->deskripsi ?: '-' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</x-admin.layout>