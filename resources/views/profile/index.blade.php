<x-dynamic-component :component="$layout" title="Profil Saya">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 fw-bold text-primary mb-0">Profile Saya</h1>
            <p class="text-secondary small mb-0">Kelola data akun dan keamanan loginmu.</p>
        </div>
        <div>
            @php
                $dashboardRoute = (auth()->user()->role === 'admin') ? route('admin.dashboard') : route('user.dashboard');
            @endphp
            <a href="{{ $dashboardRoute }}" class="btn btn-sm btn-outline-secondary px-3 shadow-sm">Kembali</a>
        </div>
    </div>

    @if (session('sukses'))
        <div class="alert alert-success alert-dismissible fade show alert-otomatis py-2 small" role="alert">
            {{ session('sukses') }}
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-3 d-flex flex-column align-items-center justify-content-center text-center">
                        @if ($user->foto)
                            <img src="{{ asset('storage/' . $user->foto) }}" alt="Foto Profil" class="rounded-circle shadow-sm object-fit-cover mb-2" style="width: 80px; height: 80px;">
                        @else
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm mb-2" style="width: 80px; height: 80px; background-color: #0f766e; font-size: 1.8rem;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif

                        <h6 class="fw-bold mb-0 text-dark">{{ $user->name }}</h6>
                        <p class="text-secondary small mb-2">{{ $user->email }}</p>

                        <span class="badge px-3 py-1 rounded-pill text-uppercase mb-3" style="background-color: #e6f4f1; color: #0f766e; font-size: 0.7rem; font-weight: 600;">
                            {{ ucfirst($user->role) }}
                        </span>

                        <div class="w-100 text-start">
                            <label for="foto" class="form-label small fw-semibold text-secondary mb-1">Ganti Foto Profil</label>
                            <input type="file" class="form-control form-control-sm @error('foto') is-invalid @enderror" id="foto" name="foto" accept="image/png, image/jpeg, image/jpg">
                            <div class="form-text text-muted" style="font-size: 0.7rem;">Maks. 2MB (JPG, JPEG, PNG)</div>
                            @error('foto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-8 d-flex flex-column gap-3">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-3">
                        <h6 class="fw-bold text-dark mb-3">Informasi Akun</h6>

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="name" class="form-label small fw-semibold text-secondary mb-1">Nama Lengkap</label>
                                <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="email" class="form-label small fw-semibold text-secondary mb-1">Email</label>
                                <input type="email" class="form-control form-control-sm @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 flex-grow-1">
                    <div class="card-body p-3">
                        <h6 class="fw-bold text-dark mb-3">Ubah Password <span class="text-muted fw-normal small">(Opsional, kosongkan jika tidak ingin mengubah)</span></h6>

                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label for="current_password" class="form-label small fw-semibold text-secondary mb-1">Password saat ini</label>
                                <div class="input-group input-group-sm">
                                    <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password">
                                    <button type="button" class="btn btn-outline-secondary tombol-lihat-password" data-target="current_password" aria-label="Tampilkan password">
                                        <svg class="ikon-mata" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/><path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/></svg>
                                        <svg class="ikon-mata-coret d-none" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l.708.709z"/><path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/><path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12-.708.708z"/></svg>
                                    </button>
                                </div>
                                @error('current_password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="password" class="form-label small fw-semibold text-secondary mb-1">Password baru</label>
                                <div class="input-group input-group-sm">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                                    <button type="button" class="btn btn-outline-secondary tombol-lihat-password" data-target="password" aria-label="Tampilkan password">
                                        <svg class="ikon-mata" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/><path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/></svg>
                                        <svg class="ikon-mata-coret d-none" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l.708.709z"/><path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/><path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12-.708.708z"/></svg>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="password_confirmation" class="form-label small fw-semibold text-secondary mb-1">Ulangi password baru</label>
                                <div class="input-group input-group-sm">
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                                    <button type="button" class="btn btn-outline-secondary tombol-lihat-password" data-target="password_confirmation" aria-label="Tampilkan password">
                                        <svg class="ikon-mata" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/><path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/></svg>
                                        <svg class="ikon-mata-coret d-none" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l.708.709z"/><path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/><path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12-.708.708z"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="text-muted" style="font-size: 0.75rem;">Minimal 8 karakter jika ingin mengganti password.</span>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ $dashboardRoute }}" class="btn btn-sm btn-outline-secondary px-4">Batal</a>
                    <button type="submit" class="btn btn-sm text-white px-4 border-0" style="background-color: #0f766e;">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </form>

    <script>
        document.addEventListener('click', function (e) {
            const tombol = e.target.closest('.tombol-lihat-password');
            if (!tombol) return;

            const input = document.getElementById(tombol.dataset.target);
            const tampil = input.type === 'password';

            input.type = tampil ? 'text' : 'password';
            tombol.querySelector('.ikon-mata').classList.toggle('d-none', tampil);
            tombol.querySelector('.ikon-mata-coret').classList.toggle('d-none', !tampil);
            tombol.setAttribute('aria-label', tampil ? 'Sembunyikan password' : 'Tampilkan password');
        });

        document.addEventListener('DOMContentLoaded', function () {
            const alertBox = document.querySelector('.alert-otomatis');
            if (alertBox && window.bootstrap) {
                setTimeout(function () {
                    bootstrap.Alert.getOrCreateInstance(alertBox).close();
                }, 3000);
            }
        });
    </script>
</x-dynamic-component>