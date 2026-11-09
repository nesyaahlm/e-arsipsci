@extends('layouts.auth')

@section('title', 'Lupa Password - PT Sucofindo Cilacap')

@section('content')
    <div class="mb-4">
        <a href="{{ route('login') }}" class="text-teal text-decoration-none small fw-semibold">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke login
        </a>
    </div>

    <div class="mb-4">
        <h3 class="fw-bold text-teal mb-2">Lupa Password?</h3>
        <p class="text-muted small">Masukkan email akunmu. Kami akan mengirim tautan untuk mengatur ulang password.</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show small" role="alert">
            {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('password.email') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="form-label small text-muted fw-semibold">Email</label>
            <div class="input-icon-group">
                <i class="bi bi-envelope field-icon"></i>
                <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="nama@sucofindo.co.id" required autofocus>
            </div>
        </div>

        <button type="submit" class="btn btn-teal w-100 py-2 mb-4">Kirim tautan reset</button>

        <div class="text-center">
            <span class="small text-muted">Tidak menerima email? <a href="#" class="text-muted text-decoration-underline">Hubungi admin arsip.</a></span>
        </div>
    </form>
@endsection