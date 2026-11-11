<x-dynamic-component :component="$layout" title="Profil Saya">
    <!-- Header Halaman dengan Tombol Kembali di Kanan -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 fw-bold text-primary mb-0">Profile Saya</h1>
            <p class="text-secondary small mb-0">Kelola data akun dan keamanan loginmu.</p>
        </div>
        <div>
            @php
                $dashboardRoute = (auth()->user()->role === 'admin') ? route('admin.dashboard') : route('user.dashboard');
            @endphp
            <a href="{{ $dashboardRoute }}" class="btn btn-sm btn-outline-secondary px-3 shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    @if (session('sukses'))
        <div class="alert alert-success alert-dismissible fade show alert-otomatis py-2 small" role="alert">
            {{ session('sukses') }}
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    <!-- 1 FORM UTAMA UNTUK SEMUA DATA (Profil, Foto, & Password) -->
    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <!-- Kolom Kiri: Foto Profil -->
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

            <!-- Kolom Kanan: Informasi Akun & Ubah Password -->
            <div class="col-12 col-lg-8 d-flex flex-column gap-3">
                <!-- Card Informasi Akun -->
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

                <!-- Card Ubah Password -->
                <div class="card border-0 shadow-sm rounded-4 flex-grow-1">
                    <div class="card-body p-3">
                        <h6 class="fw-bold text-dark mb-3">Ubah Password <span class="text-muted fw-normal small">(Opsional, kosongkan jika tidak ingin mengubah)</span></h6>

                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label for="current_password" class="form-label small fw-semibold text-secondary mb-1">Password saat ini</label>
                                <input type="password" class="form-control form-control-sm @error('current_password') is-invalid @enderror" id="current_password" name="current_password">
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="password" class="form-label small fw-semibold text-secondary mb-1">Password baru</label>
                                <input type="password" class="form-control form-control-sm @error('password') is-invalid @enderror" id="password" name="password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="password_confirmation" class="form-label small fw-semibold text-secondary mb-1">Ulangi password baru</label>
                                <input type="password" class="form-control form-control-sm" id="password_confirmation" name="password_confirmation">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="text-muted" style="font-size: 0.75rem;">Minimal 8 karakter jika ingin mengganti password.</span>
                        </div>
                    </div>
                </div>

                <!-- Tombol Aksi Global (Batal & Simpan Perubahan untuk Semuanya) -->
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ $dashboardRoute }}" class="btn btn-sm btn-outline-secondary px-4">Batal</a>
                    <button type="submit" class="btn btn-sm text-white px-4 border-0" style="background-color: #0f766e;">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </form>
</x-dynamic-component>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const alertBox = document.querySelector('.alert-otomatis');
        if (alertBox) {
            setTimeout(function () {
                let alertInstance = bootstrap.Alert.getOrCreateInstance(alertBox);
                alertInstance.close();
            }, 3000);
        }
    });
</script>
@endpush