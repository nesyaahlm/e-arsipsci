@extends('layouts.app')

@section('judul', 'Dashboard')

@section('konten')
<div class="rounded-4 p-4 mb-4" style="background: linear-gradient(135deg, #a8d5c2 0%, #d4f5e6 100%);">
    <h1 class="h3 fw-bold text-white mb-1">Selamat Datang, {{ auth()->user()->name }}!</h1>
    <p class="text-white mb-0">Kelola dan temukan dokumen arsip PT Sucofindo Cabang Cilacap dengan mudah.</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <div class="small text-primary fw-semibold">Dokumen Terarsip</div>
                <div class="display-6 fw-bold text-primary">{{ $totalDokumen }}</div>
                <div class="small text-primary">Semua Layanan</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <div class="small text-primary fw-semibold">Retensi 60 Hari Lagi</div>
                <div class="display-6 fw-bold text-primary">{{ $retensiDekat }}</div>
                <div class="small text-primary">Perlu Keputusan</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <div class="small text-primary fw-semibold">Jenis Dokumen</div>
                <div class="display-6 fw-bold text-primary">{{ $totalJenis }}</div>
                <div class="small text-primary">Di {{ $totalLayanan }} Layanan</div>
            </div>
        </div>
    </div>
</div>

@php
    $maks = max(1, $daftarLayanan->max('dokumen_count'));
@endphp

<div class="card shadow-sm">
    <div class="card-body">
        <h2 class="h5 text-primary mb-3">Dokumen per layanan</h2>
        @foreach ($daftarLayanan as $layanan)
            <div class="d-flex align-items-center gap-3 mb-2">
                <div class="text-primary" style="width: 110px;">{{ $layanan->nama }}</div>
                <div class="progress flex-grow-1" style="height: 12px;">
                    <div class="progress-bar" style="width: {{ ($layanan->dokumen_count / $maks) * 100 }}%"></div>
                </div>
                <div class="fw-bold text-primary" style="width: 30px;">{{ $layanan->dokumen_count }}</div>
            </div>
        @endforeach
    </div>
</div>
@endsection