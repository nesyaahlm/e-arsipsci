@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 small mb-3 shadow-sm border-0 d-flex align-items-center justify-content-between py-2 px-3" style="background-color: #e8f5e9; color: #1e4620;" role="alert">
        <div class="d-flex align-items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            <div>
                <strong>Sukses!</strong> {{ session('success') }}
            </div>
        </div>
        <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3 small mb-3 shadow-sm border-0 d-flex align-items-center justify-content-between py-2 px-3" style="background-color: #fde8e8; color: #9b1c1c;" role="alert">
        <div class="d-flex align-items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <div>
                <strong>Gagal!</strong> {{ session('error') }}
            </div>
        </div>
        <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show rounded-3 small mb-3 shadow-sm border-0 py-2 px-3" role="alert">
        <div class="d-flex align-items-center gap-2 mb-1">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <strong>Gagal Menyimpan Sampel:</strong>
        </div>
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<form action="{{ $aksi }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if ($metode === 'PUT')
        @method('PUT')
    @endif

    <div class="card shadow-sm rounded-4 border-0 mb-3">
        <div class="card-body p-3">
            <h6 class="fw-bold text-primary mb-2" style="font-size: 0.95rem;">1. Identitas Utama Sampel</h6>
            <div class="row g-2">
                <div class="col-12 col-md-6">
                    <label class="form-label small fw-semibold text-secondary mb-1">Kode Sampel <span class="text-danger">*</span></label>
                    <input type="text" name="kode_sampel" class="form-control form-control-sm rounded-3 @error('kode_sampel') is-invalid @enderror" 
                           value="{{ old('kode_sampel', $sampel->kode_sampel ?? '') }}" 
                           placeholder="Contoh: SMP-2611-001" required>
                    @error('kode_sampel')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label small fw-semibold text-secondary mb-1">Nama Sampel / Komoditas <span class="text-danger">*</span></label>
                    <input type="text" name="nama_sampel" class="form-control form-control-sm rounded-3 @error('nama_sampel') is-invalid @enderror" 
                           value="{{ old('nama_sampel', $sampel->nama_sampel ?? '') }}" 
                           placeholder="Contoh: Batubara Lot 2610-C" required>
                    @error('nama_sampel')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label small fw-semibold text-secondary mb-1">Layanan <span class="text-danger">*</span></label>
                    <select name="layanan_id" class="form-select form-select-sm rounded-3 @error('layanan_id') is-invalid @enderror" required>
                        <option value="" disabled {{ old('layanan_id', $sampel->layanan_id ?? '') == '' ? 'selected' : '' }}>Pilih Layanan</option>
                        @foreach ($layananList as $layanan)
                            <option value="{{ $layanan->id }}" {{ old('layanan_id', $sampel->layanan_id ?? '') == $layanan->id ? 'selected' : '' }}>
                                {{ $layanan->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('layanan_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label small fw-semibold text-secondary mb-1">Tujuan Analisis (Analysis For) <span class="text-danger">*</span></label>
                    <input type="text" name="analysis_for" class="form-control form-control-sm rounded-3 @error('analysis_for') is-invalid @enderror" 
                           value="{{ old('analysis_for', $sampel->analysis_for ?? '') }}" 
                           placeholder="Contoh: GA UMPIRE / Uji kadar sulfur" required>
                    @error('analysis_for')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm rounded-4 border-0 mb-3">
        <div class="card-body p-3">
            <h6 class="fw-bold text-primary mb-1" style="font-size: 0.95rem;">2. Informasi Kargo &amp; Pengambilan Sampel</h6>
            <p class="text-muted small mb-2" style="font-size: 0.78rem;">Kolom Shipper, Vessel, dan Bill of Lading bersifat opsional.</p>
            <div class="row g-2">
                <div class="col-12 col-md-6">
                    <label class="form-label small fw-semibold text-secondary mb-1">Shipper (Pengirim / Klien)</label>
                    <input type="text" name="shipper" class="form-control form-control-sm rounded-3" 
                           value="{{ old('shipper', $sampel->shipper ?? '') }}" 
                           placeholder="Contoh: PT. Berau Coal">
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label small fw-semibold text-secondary mb-1">Vessel (Nama Kapal / Tongkang)</label>
                    <input type="text" name="vessel" class="form-control form-control-sm rounded-3" 
                           value="{{ old('vessel', $sampel->vessel ?? '') }}" 
                           placeholder="Contoh: SPB. DIONYSOS">
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label small fw-semibold text-secondary mb-1">Bill of Lading (BL / Kuantitas Kargo)</label>
                    <input type="text" name="bill_of_lading" class="form-control form-control-sm rounded-3" 
                           value="{{ old('bill_of_lading', $sampel->bill_of_lading ?? '') }}" 
                           placeholder="Contoh: 10.024 MT">
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label small fw-semibold text-secondary mb-1">Tempat Pengambilan Sampel <span class="text-danger">*</span></label>
                    <input type="text" name="tempat_pengambilan" class="form-control form-control-sm rounded-3 @error('tempat_pengambilan') is-invalid @enderror" 
                           value="{{ old('tempat_pengambilan', $sampel->tempat_pengambilan ?? '') }}" 
                           placeholder="Contoh: Jetty 2 TUKS PLTU" required>
                    @error('tempat_pengambilan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label small fw-semibold text-secondary mb-1">Tanggal Pengambilan Sampel <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_pengambilan" class="form-control form-control-sm rounded-3 @error('tanggal_pengambilan') is-invalid @enderror" 
                           value="{{ old('tanggal_pengambilan', isset($sampel->tanggal_pengambilan) ? $sampel->tanggal_pengambilan->format('Y-m-d') : '') }}" required>
                    @error('tanggal_pengambilan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm rounded-4 border-0 mb-3">
        <div class="card-body p-3">
            <h6 class="fw-bold text-primary mb-2" style="font-size: 0.95rem;">3. Lokasi Penyimpanan Fisik &amp; Tanggal Arsip</h6>
            <div class="row g-2">
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-semibold text-secondary mb-1">Nomor Ruang / Rak <span class="text-danger">*</span></label>
                    <input type="text" name="nomor_ruang_arsip" class="form-control form-control-sm rounded-3 @error('nomor_ruang_arsip') is-invalid @enderror" 
                           value="{{ old('nomor_ruang_arsip', $sampel->nomor_ruang_arsip ?? '') }}" 
                           placeholder="Contoh: 01" required>
                    @error('nomor_ruang_arsip')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label small fw-semibold text-secondary mb-1">Nama Ruang Arsip Sampel <span class="text-danger">*</span></label>
                    <input type="text" name="nama_ruang_arsip" class="form-control form-control-sm rounded-3 @error('nama_ruang_arsip') is-invalid @enderror" 
                           value="{{ old('nama_ruang_arsip', $sampel->nama_ruang_arsip ?? '') }}" 
                           placeholder="Contoh: Ruang Arsip A" required>
                    @error('nama_ruang_arsip')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label small fw-semibold text-secondary mb-1">Tanggal Masuk Arsip <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_masuk_arsip" class="form-control form-control-sm rounded-3 @error('tanggal_masuk_arsip') is-invalid @enderror" 
                           value="{{ old('tanggal_masuk_arsip', isset($sampel->tanggal_masuk_arsip) ? $sampel->tanggal_masuk_arsip->format('Y-m-d') : date('Y-m-d')) }}" required>
                    @error('tanggal_masuk_arsip')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm rounded-4 border-0 mb-3">
        <div class="card-body p-3">
            <h6 class="fw-bold text-primary mb-2" style="font-size: 0.95rem;">4. Foto Sampel &amp; Catatan Tambahan</h6>
            <div class="row g-2">
                <div class="col-12 col-md-6">
                    <label class="form-label small fw-semibold text-secondary mb-1">Foto Sampel Fisik (Opsional)</label>

                    @if(isset($sampel) && $sampel->foto_path)
                        <div class="p-2 mb-2 bg-light border rounded-3 d-flex align-items-center gap-2">
                            <img src="{{ asset('storage/' . $sampel->foto_path) }}" alt="Foto Sampel" class="rounded-2 border object-fit-cover" style="width: 50px; height: 50px;">
                            <div>
                                <div class="small fw-semibold text-dark" style="font-size: 0.78rem;">Foto saat ini tersimpan</div>
                                <div class="text-muted" style="font-size: 0.7rem;">Pilih foto baru di bawah jika ingin mengganti.</div>
                            </div>
                        </div>
                    @endif

                    <input type="file" name="foto" id="inputFotoSampel" class="form-control form-control-sm rounded-3 @error('foto') is-invalid @enderror" accept=".jpg,.jpeg,.png">
                    <small class="text-muted d-block mt-1" style="font-size: 0.7rem;">Format: JPG, JPEG, PNG (Maks. 5 MB).</small>
                    @error('foto')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror

                    <div id="boxPreviewFoto" class="mt-2 d-none">
                        <span class="small text-muted d-block mb-1" style="font-size: 0.75rem;">Pratinjau Foto Baru:</span>
                        <img id="imgPreview" src="" alt="Pratinjau" class="rounded-3 border shadow-sm object-fit-cover" style="max-height: 120px; max-width: 100%;">
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label small fw-semibold text-secondary mb-1">Deskripsi / Catatan Fisik Sampel</label>
                    <textarea name="deskripsi" class="form-control form-control-sm rounded-3" rows="3" 
                              placeholder="Contoh: Segel kantong utuh">{{ old('deskripsi', $sampel->deskripsi ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ $kembali }}" class="btn btn-light btn-sm rounded-3 px-3 py-2 border">Batal</a>
        <button type="submit" class="btn btn-primary btn-sm rounded-3 px-3 py-2 fw-semibold">
            {{ isset($sampel) ? 'Simpan Perubahan' : 'Simpan Data Sampel' }}
        </button>
    </div>
</form>

@push('scripts')
<script>
    const inputFoto = document.getElementById('inputFotoSampel');
    const boxPreview = document.getElementById('boxPreviewFoto');
    const imgPreview = document.getElementById('imgPreview');

    if (inputFoto) {
        inputFoto.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    imgPreview.src = e.target.result;
                    boxPreview.classList.remove('d-none');
                };
                reader.readAsDataURL(this.files[0]);
            } else {
                boxPreview.classList.add('d-none');
                imgPreview.src = '';
            }
        });
    }
</script>
@endpush