    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 small mb-4 shadow-sm border-0 d-flex align-items-center justify-content-between" style="background-color: #e8f5e9; color: #1e4620;" role="alert">
            <div class="d-flex align-items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <div>
                    <strong>Sukses!</strong> {{ session('success') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="ug-modal ug-admin-look" id="unggah" data-modal @if ($errors->any() || session('error')) data-open @else hidden @endif>
        <div class="ug-modal__dialog ug-modal__dialog--lg" role="dialog" aria-modal="true" aria-labelledby="judul-form">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-primary" id="judul-form">Unggah Dokumen Baru</h5>
                    <button type="button" class="btn-close" data-modal-close aria-label="Close"></button>
                </div>

                <form method="POST" action="{{ route('user.unggah.store') }}" enctype="multipart/form-data" id="form-unggah" autocomplete="off">
                    @csrf
                    <fieldset class="ug-fieldset">
                        <div class="modal-body p-4">
                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show rounded-3 small mb-3 shadow-sm border-0 d-flex align-items-center justify-content-between" style="background-color: #fde8e8; color: #9b1c1c;" role="alert">
                                    <div class="d-flex align-items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="12" y1="8" x2="12" y2="12"></line>
                                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                        </svg>
                                        <div>
                                            <strong>Gagal!</strong> {{ session('error') }}
                                        </div>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show rounded-3 small mb-3 shadow-sm border-0" style="background-color: #fde8e8; color: #9b1c1c;" role="alert">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="12" y1="8" x2="12" y2="12"></line>
                                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                        </svg>
                                        <strong>Gagal Memproses Dokumen:</strong>
                                    </div>
                                    <ul class="mb-0 ps-4">
                                        @foreach ($errors->all() as $pesanError)
                                            <li>{{ $pesanError }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif


                            <div class="alert alert-light border rounded-3 p-3 small mb-3 text-secondary d-flex gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2a7f72" stroke-width="2" class="flex-shrink-0 mt-1">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="16" x2="12" y2="12"></line>
                                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                </svg>
                                <span>
                                    <strong>Nomor Dokumen</strong> dan <strong>Masa Retensi</strong> akan dihasilkan secara otomatis oleh sistem berdasarkan bagian dan tanggal dokumen yang dipilih.
                                    <span class="d-block mt-1">Label box arsip: Isi diambil dari judul. Nomor Rak, No. Tingkat, No. Box File, dan Tahun yang Anda isi langsung dicatat.</span>
                                </span>
                            </div>

                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="judul" class="form-label small fw-semibold text-secondary">Judul Dokumen <span class="text-danger">*</span></label>
                                    <input type="text" id="judul" name="judul" value="{{ old('judul') }}" maxlength="255" class="form-control rounded-3 @error('judul') is-invalid @enderror" placeholder="Contoh: Laporan Hasil Uji Batubara Lot 2610-C" required>
                                    @error('judul')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="bagian_id" class="form-label small fw-semibold text-secondary">Bagian <span class="text-danger">*</span></label>
                                    <select id="bagian_id" name="bagian_id" class="form-select rounded-3 @error('bagian_id') is-invalid @enderror" required>
                                        <option value="" disabled @selected(old('bagian_id') === null)>Pilih Bagian</option>
                                        @foreach ($bagianList as $bag)
                                            <option value="{{ $bag->id }}" @selected((string) old('bagian_id') === (string) $bag->id)>{{ $bag->nama }} ({{ $bag->kode }})</option>
                                        @endforeach
                                    </select>
                                    @error('bagian_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                                                <div class="col-12 col-md-6">
                                    <label for="kode_surat_id" class="form-label small fw-semibold text-secondary">Klasifikasi Kode Surat</label>
                                    <select id="kode_surat_id" name="kode_surat_id" class="form-select rounded-3 @error('kode_surat_id') is-invalid @enderror">
                                        <option value="" selected>Pilih Kode Surat (Opsional)</option>
                                        @foreach ($kodeSuratList as $ks)
                                            <option value="{{ $ks->id }}" data-bagian="{{ $ks->bagian_id }}" data-kode="{{ $ks->kode }}" data-retensi="{{ $ks->retensi_aktif }}" @selected((string) old('kode_surat_id') === (string) $ks->id)>
                                                {{ $ks->kode }} - {{ $ks->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('kode_surat_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="tanggal_dokumen" class="form-label small fw-semibold text-secondary">Tanggal Dokumen <span class="text-danger">*</span></label>
                                    <input type="date" id="tanggal_dokumen" name="tanggal_dokumen" value="{{ old('tanggal_dokumen', $hariIni) }}" class="form-control rounded-3 @error('tanggal_dokumen') is-invalid @enderror" required>
                                    @error('tanggal_dokumen')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="unit" class="form-label small fw-semibold text-secondary">Unit Kerja</label>
                                    <input type="text" id="unit" name="unit" value="{{ old('unit') }}" maxlength="100" class="form-control rounded-3 @error('unit') is-invalid @enderror" placeholder="Contoh: Cilacap">
                                    @error('unit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="file_dokumen" class="form-label small fw-semibold text-secondary">Berkas File <span class="text-danger">*</span></label>
                                    <input type="file" id="file_dokumen" name="file_dokumen" class="form-control rounded-3 @error('file_dokumen') is-invalid @enderror" accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg" required>
                                    <div class="text-danger small mt-1 fw-semibold" data-file-error @if (! $errors->has('file_dokumen')) hidden @endif>{{ $errors->first('file_dokumen') }}</div>
                                    <small class="text-muted" style="font-size: 0.72rem;" data-file-hint>Format: PDF, DOCX, XLSX (Maks. 20MB)</small>
                                </div>

                                <div class="col-12 col-md-6 col-lg-3">
                                    <label for="nomor_rak" class="form-label small fw-semibold text-secondary">Nomor Rak</label>
                                    <input type="text" id="nomor_rak" name="nomor_rak" value="{{ old('nomor_rak') }}" maxlength="30" class="form-control rounded-3 @error('nomor_rak') is-invalid @enderror" placeholder="Contoh: 01">
                                    @error('nomor_rak')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6 col-lg-3">
                                    <label for="no_tingkat" class="form-label small fw-semibold text-secondary">No. Tingkat</label>
                                    <input type="text" id="no_tingkat" name="no_tingkat" value="{{ old('no_tingkat') }}" maxlength="30" class="form-control rounded-3 @error('no_tingkat') is-invalid @enderror" placeholder="Contoh: 2">
                                    @error('no_tingkat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6 col-lg-3">
                                    <label for="no_box" class="form-label small fw-semibold text-secondary">No. Box File</label>
                                    <input type="text" id="no_box" name="no_box" value="{{ old('no_box') }}" maxlength="30" class="form-control rounded-3 @error('no_box') is-invalid @enderror" placeholder="Contoh: 04">
                                    @error('no_box')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6 col-lg-3">
                                    <label for="tahun_arsip" class="form-label small fw-semibold text-secondary">Tahun</label>
                                    <input type="text" id="tahun_arsip" name="tahun_arsip" value="{{ old('tahun_arsip') }}" maxlength="50" inputmode="numeric" class="form-control rounded-3 @error('tahun_arsip') is-invalid @enderror" placeholder="Contoh: 2026 dan 2027">
                                    @error('tahun_arsip')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted" style="font-size: 0.72rem;">Kosong = tahun tanggal dokumen.</small>
                                </div>

                                <div class="col-12">
                                    <label for="deskripsi" class="form-label small fw-semibold text-secondary">Deskripsi / Isi Dokumen</label>
                                    <textarea id="deskripsi" name="deskripsi" class="form-control rounded-3 @error('deskripsi') is-invalid @enderror" rows="2" placeholder="Contoh: - Kontrak Pengadaan">{{ old('deskripsi') }}</textarea>
                                    @error('deskripsi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer border-0 pt-0 pb-4 px-4">
                            <button type="button" class="btn btn-light rounded-3 px-3" data-modal-close>Batal</button>
                            <button type="submit" class="btn btn-primary rounded-3 px-4" data-submit>Unggah &amp; Simpan</button>
                        </div>
                    </fieldset>

                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const selectBagianUser = document.getElementById('bagian_id');
                            const selectKodeSuratUser = document.getElementById('kode_surat_id');

                            if (selectBagianUser && selectKodeSuratUser) {
                                const originalOptions = Array.from(selectKodeSuratUser.options);

                                function filterKodeSuratUser() {
                                    const selectedBagianId = selectBagianUser.value;
                                    const currentValue = selectKodeSuratUser.value;

                                    selectKodeSuratUser.innerHTML = '';

                                    originalOptions.forEach(opt => {
                                        if (!opt.value || !selectedBagianId || opt.dataset.bagian === selectedBagianId) {
                                            selectKodeSuratUser.appendChild(opt.cloneNode(true));
                                        }
                                    });

                                    if (Array.from(selectKodeSuratUser.options).some(o => o.value === currentValue)) {
                                        selectKodeSuratUser.value = currentValue;
                                    } else {
                                        selectKodeSuratUser.selectedIndex = 0;
                                    }
                                }

                                selectBagianUser.addEventListener('change', filterKodeSuratUser);
                                filterKodeSuratUser();
                            }
                        });
                    </script>
                </form>
            </div>
        </div>
    </div>

    <div class="ug-popup ug-admin-look" data-popup hidden>
        <div class="card border-0 shadow-lg rounded-4 p-4 text-center mx-3" style="max-width: 420px; width: 100%; background: #ffffff;" role="alertdialog" aria-modal="true" aria-labelledby="popup-judul">
            <div class="d-inline-flex align-items-center justify-content-center mx-auto mb-3 rounded-circle" style="width: 60px; height: 60px; background-color: #fee2e2; color: #dc2626;">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
            </div>
            <h5 class="fw-bold text-dark mb-2" id="popup-judul">Ukuran Berkas Terlalu Besar</h5>
            <p class="text-muted small mb-3">
                Berkas <strong class="text-dark" data-popup-nama></strong> melebihi batas maksimal yang diizinkan sistem.
            </p>
            <div class="p-2 mb-3 rounded-3 bg-light border small text-secondary">
                <div>Ukuran berkas Anda: <strong class="text-danger" data-popup-ukuran></strong></div>
                <div>Batas maksimal berkas: <strong>20 MB</strong></div>
            </div>
            <p class="text-muted small mb-3">Silakan pilih kembali berkas lain yang berukuran lebih kecil.</p>
            <button type="button" class="btn btn-primary rounded-3 w-100 py-2 fw-semibold" data-popup-close>Saya Mengerti</button>
        </div>
    </div>