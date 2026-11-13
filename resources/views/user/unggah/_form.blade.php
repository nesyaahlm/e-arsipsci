    @if (session('success'))
        <div class="ug-alert ug-alert--success" role="alert">
            <div class="ug-alert__body">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ug-svg" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                <div>
                    <strong>Sukses!</strong> {{ session('success') }}
                </div>
            </div>
            <button type="button" class="ug-alert__close" data-alert-close aria-label="Tutup">&times;</button>
        </div>
    @endif

    <div class="ug-modal" id="unggah" data-modal @if ($errors->any() || session('error')) data-open @else hidden @endif>
        <div class="ug-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="judul-form">
            <button type="button" class="ug-modal__close" data-modal-close aria-label="Tutup">&times;</button>
        @if (session('error'))
            <div class="ug-alert ug-alert--error" role="alert">
                <div class="ug-alert__body">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ug-svg" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <div><strong>Gagal!</strong> {{ session('error') }}</div>
                </div>
                <button type="button" class="ug-alert__close" data-alert-close aria-label="Tutup">&times;</button>
            </div>
        @endif

        @if ($errors->any())
            <div class="ug-alert ug-alert--error ug-alert--list" role="alert">
                <div class="ug-alert__body">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ug-svg" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <strong>Gagal Memproses Dokumen:</strong>
                </div>
                <ul>
                    @foreach ($errors->all() as $pesanError)
                        <li>{{ $pesanError }}</li>
                    @endforeach
                </ul>
                <button type="button" class="ug-alert__close" data-alert-close aria-label="Tutup">&times;</button>
            </div>
        @endif

        @if ($layanan === null)
            <div class="ug-alert ug-alert--warning" role="alert">
                <div class="ug-alert__body">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ug-svg" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <div>Akun Anda belum terhubung ke layanan, jadi belum bisa mengunggah dokumen. Hubungi admin untuk mengaturnya.</div>
                </div>
            </div>
        @endif

            <section class="ua-card ug-card" aria-labelledby="judul-form">
                <h2 class="ug-title" id="judul-form">Unggah Dokumen Baru</h2>

                <form method="POST" action="{{ route('user.unggah.store') }}" enctype="multipart/form-data" id="form-unggah" autocomplete="off">
                    @csrf

                    <fieldset class="ug-fieldset" @disabled($layanan === null)>
                        <div class="ug-info">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ug-svg" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                            <span><strong>Nomor Dokumen</strong> dan <strong>Masa Retensi</strong> akan dihasilkan secara otomatis oleh sistem berdasarkan jenis dokumen dan tanggal dokumen yang dipilih.</span>
                        </div>

                        <div class="ug-row ug-row--full">
                            <div class="ug-field">
                                <label for="judul" class="ug-label">Judul Dokumen <span class="ug-req">*</span></label>
                                <input type="text" id="judul" name="judul" value="{{ old('judul') }}" maxlength="255" placeholder="Contoh: Laporan Hasil Uji Batubara Lot 2610-C" class="ug-control @error('judul') is-invalid @enderror" required>
                                @error('judul')
                                    <p class="ug-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="ug-row">
                            <div class="ug-field">
                                <label for="layanan_id" class="ug-label">Layanan <span class="ug-req">*</span></label>
                                <select id="layanan_id" class="ug-control" disabled>
                                    <option>{{ $layanan?->nama ?? 'Belum terhubung ke layanan' }}</option>
                                </select>
                                <p class="ug-hint">Layanan mengikuti akun Anda.</p>
                            </div>

                            <div class="ug-field">
                                <label for="jenis_dokumen_id" class="ug-label">Jenis Dokumen <span class="ug-req">*</span></label>
                                <select id="jenis_dokumen_id" name="jenis_dokumen_id" class="ug-control @error('jenis_dokumen_id') is-invalid @enderror" required>
                                    <option value="" disabled @selected(old('jenis_dokumen_id') === null)>Pilih Jenis Dokumen</option>
                                    @foreach ($jenisList as $jenis)
                                        <option value="{{ $jenis->id }}" @selected((string) old('jenis_dokumen_id') === (string) $jenis->id)>{{ $jenis->nama }} ({{ $jenis->kode }})</option>
                                    @endforeach
                                </select>
                                @error('jenis_dokumen_id')
                                    <p class="ug-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="ug-row">
                            <div class="ug-field">
                                <label for="tanggal_dokumen" class="ug-label">Tanggal Dokumen <span class="ug-req">*</span></label>
                                <input type="date" id="tanggal_dokumen" name="tanggal_dokumen" value="{{ old('tanggal_dokumen', $hariIni) }}" class="ug-control @error('tanggal_dokumen') is-invalid @enderror" required>
                                @error('tanggal_dokumen')
                                    <p class="ug-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="ug-field">
                                <label for="unit" class="ug-label">Unit Kerja</label>
                                <input type="text" id="unit" name="unit" value="{{ old('unit') }}" maxlength="100" placeholder="Contoh: Cilacap" class="ug-control @error('unit') is-invalid @enderror">
                                @error('unit')
                                    <p class="ug-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="ug-row">
                            <div class="ug-field">
                                <label for="file_dokumen" class="ug-label">Berkas File <span class="ug-req">*</span></label>
                                <input type="file" id="file_dokumen" name="file_dokumen" class="ug-control ug-control--file @error('file_dokumen') is-invalid @enderror" accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg" required>
                                <div class="ug-error ug-error--strong" data-file-error @if (! $errors->has('file_dokumen')) hidden @endif>{{ $errors->first('file_dokumen') }}</div>
                                <small class="ug-hint" data-file-hint>Format: PDF, DOCX, XLSX (Maks. 20MB)</small>
                            </div>

                            <div class="ug-field">
                                <label for="nomor_rak" class="ug-label">Nomor Rak</label>
                                <input type="text" id="nomor_rak" name="nomor_rak" value="{{ old('nomor_rak') }}" maxlength="30" placeholder="Contoh: 01" class="ug-control @error('nomor_rak') is-invalid @enderror">
                                @error('nomor_rak')
                                    <p class="ug-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="ug-row">
                            <div class="ug-field">
                                <label for="no_tingkat" class="ug-label">No. Tingkat</label>
                                <input type="text" id="no_tingkat" name="no_tingkat" value="{{ old('no_tingkat') }}" maxlength="30" placeholder="Contoh: 2" class="ug-control @error('no_tingkat') is-invalid @enderror">
                                @error('no_tingkat')
                                    <p class="ug-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="ug-field">
                                <label for="no_box" class="ug-label">No. Box File</label>
                                <input type="text" id="no_box" name="no_box" value="{{ old('no_box') }}" maxlength="30" placeholder="Contoh: 04" class="ug-control @error('no_box') is-invalid @enderror">
                                @error('no_box')
                                    <p class="ug-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="ug-row ug-row--full">
                            <div class="ug-field">
                                <label for="deskripsi" class="ug-label">Deskripsi / Isi Dokumen</label>
                                <textarea id="deskripsi" name="deskripsi" rows="2" placeholder="Contoh: - Kontrak Pengadaan" class="ug-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi') }}</textarea>
                                @error('deskripsi')
                                    <p class="ug-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="ug-note">
                            <strong>Label box arsip</strong>
                            <p>Portofolio mengikuti layanan akun Anda, Tahun diambil dari tanggal dokumen, dan Isi diambil dari judul. Kode Rak dapat ditetapkan admin; Nomor Rak, No. Tingkat, dan No. Box File yang Anda isi langsung dicatat.</p>
                        </div>

                        <div class="ug-actions">
                            <button type="button" class="ug-btn ug-btn--light" data-modal-close>Batal</button>
                            <button type="submit" class="ug-btn ug-btn--primary" data-submit>Unggah &amp; Simpan</button>
                        </div>
                    </fieldset>
                </form>
            </section>
        </div>
    </div>

    <div class="ug-popup" data-popup hidden>
        <div class="ug-popup__card" role="alertdialog" aria-modal="true" aria-labelledby="popup-judul">
            <span class="ug-popup__icon"><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ug-svg" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg></span>
            <h2 class="ug-popup__title" id="popup-judul">Ukuran Berkas Terlalu Besar</h2>
            <p class="ug-popup__text">Berkas <strong data-popup-nama></strong> melebihi batas maksimal yang diizinkan sistem.</p>
            <div class="ug-popup__box">
                <div>Ukuran berkas Anda: <strong class="ug-popup__danger" data-popup-ukuran></strong></div>
                <div>Batas maksimal berkas: <strong>20 MB</strong></div>
            </div>
            <p class="ug-popup__text">Silakan pilih kembali berkas lain yang berukuran lebih kecil.</p>
            <button type="button" class="ug-btn ug-btn--primary ug-btn--block" data-popup-close>Saya Mengerti</button>
        </div>
    </div>
