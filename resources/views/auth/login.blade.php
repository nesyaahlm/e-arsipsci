<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk - E-Arsip</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="bg-body-tertiary">
<div class="container min-vh-100 d-flex align-items-center">
    <div class="row justify-content-center w-100">
        <div class="col-12 col-sm-8 col-md-6 col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <div class="pb-3 mb-4 border-bottom border-2 border-primary">
                        <div class="fs-5 fw-bold text-primary">PT SUCOFINDO</div>
                        <div class="small text-secondary">E-Arsip - Sistem Elektronik Arsip</div>
                    </div>
                    <form method="POST" action="{{ route('login.proses') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" value="{{ old('email') }}" required autofocus>
                            <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" id="password" name="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" required>
                            <div class="invalid-feedback">{{ $errors->first('password') }}</div>
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" id="ingat" name="ingat" value="1" class="form-check-input">
                            <label for="ingat" class="form-check-label">Ingat saya</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Masuk</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>