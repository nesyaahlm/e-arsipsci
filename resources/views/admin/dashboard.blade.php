@extends('layouts.app')

@section('judul', 'Dashboard')

@section('konten')
<div class="rounded-4 p-4 mb-4 shadow-sm" style="background: linear-gradient(90deg, #9ec8b5 0%, #ccefe0 100%);">
    <h1 class="h3 fw-bold text-white mb-1">Selamat Datang, {{ auth()->user()->name }}!</h1>
    <p class="text-white mb-3" style="font-size: 0.95rem; opacity: 0.95;">
        Kelola dan temukan dokumen arsip PT Sucofindo Cabang Cilacap dengan mudah.
    </p>

    <form action="{{ Route::has('dokumen.index') ? route('dokumen.index') : '#' }}" method="GET">
        <div class="position-relative" style="max-width: 100%;">
            <!-- Ikon Kaca Pembesar (Search) -->
            <span class="position-absolute top-50 start-0 translate-middle-y ps-3 text-secondary d-flex align-items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6c757d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </span>

            <input type="text" 
                   name="cari" 
                   class="form-control rounded-pill border-0 shadow-sm ps-5 py-2" 
                   placeholder="Cari dokumen (nomor, judul, kata kunci).." 
                   style="font-size: 0.92rem; color: #495057;">
        </div>
    </form>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="card h-100 shadow-sm rounded-4" style="border: 1.5px solid #cfdedb;">
            <div class="card-body p-4">
                <div class="small text-primary fw-semibold mb-1">Dokumen Terarsip</div>
                <div class="display-6 fw-bold text-primary mb-1">{{ $totalDokumen }}</div>
                <div class="small text-muted">Semua Layanan</div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="card h-100 shadow-sm rounded-4" style="border: 1.5px solid #cfdedb;">
            <div class="card-body p-4">
                <div class="small text-primary fw-semibold mb-1">Retensi 60 Hari Lagi</div>
                <div class="display-6 fw-bold text-primary mb-1">{{ $retensiDekat }}</div>
                <div class="small text-muted">Perlu Keputusan</div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="card h-100 shadow-sm rounded-4" style="border: 1.5px solid #cfdedb;">
            <div class="card-body p-4">
                <div class="small text-primary fw-semibold mb-1">Jenis Dokumen</div>
                <div class="display-6 fw-bold text-primary mb-1">{{ $totalJenis }}</div>
                <div class="small text-muted">Di {{ $totalLayanan }} Layanan</div>
            </div>
        </div>
    </div>
</div>

@php
    $maks = max(1, $daftarLayanan->max('dokumen_count'));
@endphp

<div class="card shadow-sm rounded-4" style="border: 1.5px solid #cfdedb;">
    <div class="card-body p-4">
        <h2 class="h5 fw-bold text-primary mb-4">Dokumen per layanan</h2>

        @foreach ($daftarLayanan as $layanan)
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="text-secondary fw-semibold" style="width: 120px; font-size: 0.95rem;">
                    {{ $layanan->nama }}
                </div>

                <div class="progress flex-grow-1 rounded-pill" style="height: 12px; background-color: #e9ecef;">
                    <div class="progress-bar rounded-pill" 
                         role="progressbar" 
                         style="width: {{ ($layanan->dokumen_count / $maks) * 100 }}%; background-color: #5aa89b;">
                    </div>
                </div>

                <div class="fw-bold text-secondary text-end" style="width: 35px; font-size: 0.95rem;">
                    {{ $layanan->dokumen_count }}
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection