@push('styles')
    <link rel="stylesheet" href="{{ asset('css/user/unggah.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/user/unggah.js') }}" defer></script>
@endpush

<x-layouts.user title="Unggah Dokumen">
    <div class="ua-page-header">
        <h1 class="ua-page-title">Unggah Dokumen</h1>
        <p class="ua-page-subtitle">Dokumen akan diperiksa admin sebelum tampil di arsip.</p>
    </div>

    @if (session('success'))
        <div class="ug-alert ug-alert--success" role="status">
            <span>{{ session('success') }}</span>
            <a href="{{ route('user.dokumen-saya.index') }}">Lihat di Dokumen Saya</a>
        </div>
    @endif

    @if (session('error'))
        <div class="ug-alert ug-alert--error" role="alert">
            <span>{{ session('error') }}</span>
        </div>
    @elseif ($errors->any())
        <div class="ug-alert ug-alert--error" role="alert">
            <span>Periksa kembali isian Anda. Ada {{ $errors->count() }} bagian yang perlu diperbaiki.</span>
        </div>
    @endif

    @if ($layanan === null)
        <div class="ug-alert ug-alert--warning" role="alert">
            <span>Akun Anda belum terhubung ke layanan, jadi belum bisa mengunggah dokumen. Hubungi admin untuk mengaturnya.</span>
        </div>
    @endif

    <div class="ug-grid">
        <section class="ua-card" aria-labelledby="judul-form">
            <h2 class="ug-title" id="judul-form">Data Dokumen</h2>

            <form method="POST" action="{{ route('user.unggah.store') }}" enctype="multipart/form-data" id="form-unggah">
                @csrf

                <fieldset class="ug-fieldset" @disabled($layanan === null)>
                    <div class="ug-field">
                        <label for="judul" class="ug-label">Judul Dokumen</label>
                        <input type="text" id="judul" name="judul" value="{{ old('judul') }}" maxlength="255" placeholder="Contoh: Laporan Hasil Uji Batubara" class="ug-input @error('judul') is-invalid @enderror" required>
                        @error('judul')
                            <p class="ug-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="ug-row">
                        <div class="ug-field">
                            <label for="jenis_dokumen_id" class="ug-label">Jenis Dokumen</label>
                            <select id="jenis_dokumen_id" name="jenis_dokumen_id" class="ug-input @error('jenis_dokumen_id') is-invalid @enderror" required>
                                <option value="">Pilih jenis dokumen</option>
                                @foreach ($jenisList as $jenis)
                                    <option value="{{ $jenis->id }}" @selected((string) old('jenis_dokumen_id') === (string) $jenis->id)>{{ $jenis->nama }} ({{ $jenis->kode }})</option>
                                @endforeach
                            </select>
                            @error('jenis_dokumen_id')
                                <p class="ug-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="ug-field">
                            <label for="tanggal_dokumen" class="ug-label">Tanggal Dokumen</label>
                            <input type="date" id="tanggal_dokumen" name="tanggal_dokumen" value="{{ old('tanggal_dokumen') }}" max="{{ $hariIni }}" class="ug-input @error('tanggal_dokumen') is-invalid @enderror" required>
                            <p class="ug-hint">Tahun arsip: <strong data-tahun>-</strong></p>
                            @error('tanggal_dokumen')
                                <p class="ug-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="ug-row">
                        <div class="ug-field">
                            <label for="unit" class="ug-label">Unit Kerja <span class="ug-opt">(opsional)</span></label>
                            <input type="text" id="unit" name="unit" value="{{ old('unit') }}" list="daftar-unit" maxlength="100" placeholder="Contoh: Laboratorium" autocomplete="off" class="ug-input @error('unit') is-invalid @enderror">
                            <datalist id="daftar-unit">
                                @foreach ($unitList as $unit)
                                    <option value="{{ $unit }}"></option>
                                @endforeach
                            </datalist>
                            @error('unit')
                                <p class="ug-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="ug-field">
                            <label for="portofolio" class="ug-label">Layanan</label>
                            <input type="text" id="portofolio" value="{{ $layanan?->nama ?? '-' }}" class="ug-input" readonly tabindex="-1">
                        </div>
                    </div>

                    <div class="ug-field">
                        <label for="deskripsi" class="ug-label">Deskripsi <span class="ug-opt">(opsional)</span></label>
                        <textarea id="deskripsi" name="deskripsi" rows="3" maxlength="2000" placeholder="Isi atau keterangan singkat dokumen" class="ug-input @error('deskripsi') is-invalid @enderror">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <p class="ug-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="ug-field">
                        <span class="ug-label">Berkas</span>

                        <label for="file_dokumen" class="ug-drop @error('file_dokumen') is-invalid @enderror" data-drop>
                            <input type="file" id="file_dokumen" name="file_dokumen" accept=".pdf,.doc,.docx,.xls,.xlsx" class="ug-drop__input" required>
                            <span class="ug-drop__icon"><x-user.icon name="upload" :size="26" /></span>
                            <span class="ug-drop__text"><strong>Pilih berkas</strong> atau seret ke sini</span>
                            <span class="ug-drop__hint">PDF, Word, atau Excel. Maksimal 20 MB.</span>
                        </label>

                        <div class="ug-file" data-file hidden>
                            <span class="ug-file__icon"><x-user.icon name="file" :size="22" /></span>
                            <span class="ug-file__body">
                                <strong class="ug-file__name" data-file-name></strong>
                                <span class="ug-file__meta" data-file-meta></span>
                            </span>
                            <button type="button" class="ug-file__remove" data-file-remove>Hapus</button>
                        </div>

                        <p class="ug-error" data-file-error @if (! $errors->has('file_dokumen')) hidden @endif>{{ $errors->first('file_dokumen') }}</p>
                    </div>

                    <div class="ug-note">
                        <strong>Label box arsip</strong>
                        <p>Portofolio mengikuti layanan akun Anda, Tahun diambil dari tanggal dokumen, dan Isi diambil dari judul. Kode Rak, Nomor Rak, No. Tingkat, dan No. Box File diisi admin setelah dokumen diperiksa.</p>
                    </div>

                    <div class="ug-actions">
                        <button type="submit" class="ua-btn ua-btn--primary ug-submit" data-submit>
                            <span class="ug-submit__spinner" data-spinner hidden></span>
                            <span data-submit-text>Kirim Dokumen</span>
                        </button>
                        <a href="{{ route('user.dashboard') }}" class="ua-btn ua-btn--outline">Batal</a>
                    </div>

                    <div class="ug-progress" data-progress hidden role="progressbar" aria-label="Sedang mengunggah"><span></span></div>
                </fieldset>
            </form>
        </section>

        <aside class="ua-card" aria-labelledby="judul-terakhir">
            <h2 class="ug-title" id="judul-terakhir">Status Unggahan Terakhir</h2>

            @forelse ($terakhir as $item)
                <a href="{{ route('user.dokumen-saya.show', $item['id']) }}" class="ug-recent">
                    <span class="ug-recent__title">{{ $item['judul'] }}</span>
                    <span class="ug-recent__meta">{{ $item['jenis'] }} · {{ $item['tanggal'] }}</span>
                    <span class="ua-badge ua-badge--{{ $item['status'] }}">{{ $item['status_label'] }}</span>
                </a>
            @empty
                <div class="ug-empty">
                    <span class="ug-empty__icon"><x-user.icon name="upload" :size="26" /></span>
                    <strong class="ug-empty__title">Belum ada unggahan</strong>
                    <span class="ug-empty__text">Dokumen yang Anda kirim akan muncul di sini beserta status pemeriksaannya.</span>
                </div>
            @endforelse

            @if ($terakhir->isNotEmpty())
                <a href="{{ route('user.dokumen-saya.index') }}" class="ug-more">Lihat semua dokumen saya</a>
            @endif
        </aside>
    </div>
</x-layouts.user>   