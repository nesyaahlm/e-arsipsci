@extends('layouts.auth')

@section('title', 'Tautan Terkirim - PT Sucofindo Cilacap')

@section('content')
    <div class="text-center py-2">
        
        <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-4 mb-3 border border-2" style="border-color: #1b6b63 !important; background-color: #f0fdf4;">
            <i class="bi bi-envelope-check fs-2 text-teal"></i>
        </div>

        <h3 class="fw-bold text-teal mb-3">Cek Email Kamu</h3>

        <p class="text-muted small mb-4" style="line-height: 1.6;">
            Tautan reset sudah dikirim ke <strong class="text-dark">{{ $maskedEmail }}</strong>.<br>
            Tautan berlaku 30 menit.
        </p>

        <a href="{{ route('login') }}" class="btn btn-teal w-100 py-2 mb-3">Kembali ke login</a>

        @if(session('demo_token'))
            <div class="mt-4 p-2 bg-light border rounded small text-start">
                <span class="text-muted d-block fw-semibold mb-1">Mode Pengujian Lokal:</span>
                <a href="{{ route('password.reset', ['token' => session('demo_token'), 'email' => session('demo_email')]) }}" class="text-teal text-break" style="font-size: 0.75rem;">
                    Klik di sini untuk langsung mencoba Form Password Baru &rarr;
                </a>
            </div>
        @endif
    </div>
@endsection