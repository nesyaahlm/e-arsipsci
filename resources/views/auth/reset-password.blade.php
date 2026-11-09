@extends('layouts.auth')

@section('title', 'Buat Password Baru - PT Sucofindo Cilacap')

@section('content')
    <div class="mb-4">
        <h3 class="fw-bold text-teal mb-2">Password Baru</h3>
        <p class="text-muted small">Silakan masukkan password baru Anda untuk akun <strong class="text-dark">{{ $email }}</strong>.</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show small" role="alert">
            {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('password.update') }}" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">

        <div class="mb-3">
            <label class="form-label small text-muted fw-semibold">Password Baru</label>
            <div class="input-icon-group">
                <i class="bi bi-lock field-icon"></i>
                <input type="password" name="password" class="form-control" placeholder="Minimal 8 karakter" required autofocus>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label small text-muted fw-semibold">Konfirmasi Password Baru</label>
            <div class="input-icon-group">
                <i class="bi bi-shield-check field-icon"></i>
                <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password baru" required>
            </div>
        </div>

        <button type="submit" class="btn btn-teal w-100 py-2">Simpan Password Baru</button>
    </form>
@endsection