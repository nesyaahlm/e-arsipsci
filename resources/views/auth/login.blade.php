@extends('layouts.auth')

@section('title', 'Login - Sistem E-Arsip PT Sucofindo Cilacap')

@section('content')
    <div class="text-center mb-3">
        <img src="{{ asset('assets/sucofindo-nobg.png') }}" 
             alt="Logo PT Sucofindo" 
             class="img-fluid" 
             style="max-height: 55px; width: auto; object-fit: contain;">
    </div>

    <div class="text-center mb-4">
        <h3 class="fw-bold text-teal mb-1">Selamat Datang!</h3>
        <p class="text-muted small mb-0">Masuk ke akun Anda untuk mengakses<br>Sistem E-Arsip PT Sucofindo Cilacap</p>
    </div>

    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show small" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show small" role="alert">
            {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('login.post') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label small text-muted fw-semibold">Username / Email</label>
            <div class="input-icon-group">
                <i class="bi bi-person field-icon"></i>
                <input type="text" name="login" value="{{ old('login') }}" class="form-control @error('login') is-invalid @enderror" placeholder="Masukkan username atau email" required autofocus>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label small text-muted fw-semibold">Password</label>
            <div class="input-icon-group">
                <i class="bi bi-lock field-icon"></i>
                <input type="password" id="passwordInput" name="password" class="form-control" placeholder="••••••••" required>
                <i class="bi bi-eye toggle-password" id="togglePassword"></i>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
                <label class="form-check-label small text-muted" for="rememberMe">
                    Ingat saya
                </label>
            </div>
            <a href="{{ route('password.request') }}" class="small text-teal text-decoration-none fw-semibold">Lupa password?</a>
        </div>

        <button type="submit" class="btn btn-teal w-100 py-2 mb-4">Login</button>

        <div class="text-center text-muted" style="font-size: 0.72rem; line-height: 1.4;">
            <p class="mb-1 fw-semibold">&copy; 2026 PT Sucofindo Cabang Cilacap</p>
            <p class="mb-0 text-secondary">Jl. Soekarno Hatta No.280, Kemambang, Menganti, Kec. Kesugihan, Kabupaten Cilacap, Jawa Tengah 53274</p>
        </div>
    </form>
@endsection

@section('scripts')
<script>
    // Toggle Show/Hide Password
    const togglePassword = document.querySelector('#togglePassword');
    const passwordInput = document.querySelector('#passwordInput');

    togglePassword.addEventListener('click', function () {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.classList.toggle('bi-eye');
        this.classList.toggle('bi-eye-slash');
    });
</script>
@endsection
