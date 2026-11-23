<x-admin.layout title="Portofolio Sampel">
    <h1 class="h3 fw-bold text-primary mb-1">Portofolio Sampel &amp; Komoditas</h1>
    <p class="text-primary mb-4">Daftar kategori komoditas portofolio beserta standarisasi klasifikasi sampel laboratorium.</p>

    @if (session('sukses'))
        <div class="alert alert-success alert-dismissible fade show alert-otomatis" role="alert">
            {{ session('sukses') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif
    @if (session('galat'))
        <div class="alert alert-danger alert-dismissible fade show alert-otomatis" role="alert">
            {{ session('galat') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show alert-otomatis" role="alert">
            <div class="fw-semibold mb-1">Gagal menyimpan klasifikasi:</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <form action="{{ route('portofolio.index') }}" method="GET" class="d-flex gap-2 flex-grow-1" style="max-width: 420px;">
            <input type="text" name="cari" value="{{ $cari ?? '' }}" class="form-control form-control-sm" placeholder="Cari kode portofolio, nama, atau komoditas...">
            <button type="submit" class="btn btn-sm btn-primary">Cari</button>
            @if(request('cari'))
                <a href="{{ route('portofolio.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            @endif
        </form>

        <button type="button" id="btn-tambah-portofolio" class="btn btn-sm btn-tambah-layanan">+ Tambah Portofolio</button>
    </div>

    <div class="panel-layanan" style="border-top-left-radius: 14px;">
        @php
            $modeEdit = $errors->portofolio->any() && old('edit_id');
        @endphp
        <form method="POST"
              action="{{ $modeEdit ? route('portofolio.update', old('edit_id')) : route('portofolio.store') }}"
              data-url-tambah="{{ route('portofolio.store') }}"
              id="form-portofolio"
              class="form-jenis d-none">
            @csrf
            <input type="hidden" name="_method" value="PUT" id="portofolio-method" @disabled(! $modeEdit)>
            <input type="hidden" name="edit_id" id="portofolio-edit-id" value="{{ $modeEdit ? old('edit_id') : '' }}">
            <h2 class="form-jenis-judul" id="judul-form-portofolio">{{ $modeEdit ? 'Form Edit Portofolio' : 'Form Tambah Portofolio' }}</h2>

            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label for="portofolio-kode" class="form-label">Kode Portofolio</label>
                    <input type="text" id="portofolio-kode" name="kode" maxlength="10" required
                           class="form-control text-uppercase {{ $errors->portofolio->has('kode') ? 'is-invalid' : '' }}"
                           value="{{ $errors->portofolio->any() ? old('kode') : '' }}">
                    <div class="invalid-feedback">{{ $errors->portofolio->first('kode') }}</div>
                </div>
                <div class="col-12 col-md-6">
                    <label for="portofolio-nama" class="form-label">Nama Portofolio / Komoditas</label>
                    <input type="text" id="portofolio-nama" name="nama" maxlength="255" required
                           class="form-control {{ $errors->portofolio->has('nama') ? 'is-invalid' : '' }}"
                           value="{{ $errors->portofolio->any() ? old('nama') : '' }}">
                    <div class="invalid-feedback">{{ $errors->portofolio->first('nama') }}</div>
                </div>
                <div class="col-12 col-md-6">
                    <label for="portofolio-retensi" class="form-label">Retensi Default Sampel (kosong = khusus)</label>
                    <div class="input-group">
                        <input type="number" id="portofolio-retensi" name="retensi_tahun" min="1" max="100"
                               class="form-control {{ $errors->portofolio->has('retensi_tahun') ? 'is-invalid' : '' }}"
                               style="border: 1px solid #2a7f72; border-top-right-radius: 0; border-bottom-right-radius: 0;"
                               value="{{ $errors->portofolio->any() ? old('retensi_tahun') : '' }}">
                        <select id="portofolio-retensi-satuan" name="retensi_satuan" class="form-select"
                                style="max-width: 110px; border: 1px solid #2a7f72; border-left: none; border-top-left-radius: 0; border-bottom-left-radius: 0; border-top-right-radius: 8px; border-bottom-right-radius: 8px;">
                            <option value="bulan" {{ old('retensi_satuan') === 'bulan' ? 'selected' : '' }}>Bulan</option>
                            <option value="tahun" {{ old('retensi_satuan') === 'tahun' ? 'selected' : '' }}>Tahun</option>
                        </select>
                    </div>
                    <div class="invalid-feedback d-block">{{ $errors->portofolio->first('retensi_tahun') }}</div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="submit" id="btn-simpan-portofolio" class="btn-simpan">{{ $modeEdit ? 'Simpan Perubahan' : '+ Tambah' }}</button>
                <button type="button" id="btn-kembali-portofolio" class="btn-batal">Kembali</button>
            </div>
        </form>

        <div class="table-responsive" id="tabel-portofolio-wrap">
            <table class="table table-hover align-middle mb-0 tabel-jenis">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">No</th>
                        <th class="text-center" style="width: 90px;">Kode</th>
                        <th class="text-start" style="width: 230px;">Nama Portofolio</th>
                        <th class="text-start">Klasifikasi Sampel (Komoditas)</th>
                        <th class="text-center" style="width: 100px;">Retensi</th>
                        <th class="text-center" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($daftarPortofolio as $index => $item)
                        @php
                            $bisaKelolaPorto = !is_null($item->cabang_id);
                        @endphp
                        <tr>
                            <td class="text-center">{{ $daftarPortofolio->firstItem() + $index }}</td>
                            <td class="fw-bold text-center">
                                <span class="badge rounded-pill px-2.5 py-1 font-monospace" style="background-color: #e6f4f1; color: #277a72; font-size: 0.82rem;">
                                    {{ $item->kode }}
                                </span>
                            </td>
                            <td class="text-start">
                                <div class="fw-semibold text-dark">{{ $item->nama }}</div>
                                @if (!$bisaKelolaPorto)
                                    <span class="badge bg-primary-subtle text-primary border" style="font-size: 0.7rem;">Standar Nasional</span>
                                @else
                                    <span class="badge bg-success-subtle text-success border" style="font-size: 0.7rem;">Khusus Cabang</span>
                                @endif
                            </td>
                            <td class="text-start">
                                @if ($item->kodeSurat && $item->kodeSurat->isNotEmpty())
                                    <div class="d-flex flex-wrap gap-1 align-items-center">
                                        @foreach ($item->kodeSurat as $ks)
                                            <span class="badge bg-light text-dark border px-2 py-1 font-monospace" style="font-size: 0.75rem;" title="{{ $ks->nama }} (Retensi Aktif: {{ $ks->retensi_aktif }} Thn)">
                                                {{ $ks->kode }}
                                            </span>
                                        @endforeach
                                        <button type="button"
                                                class="btn btn-link btn-sm p-0 ms-1 text-decoration-none text-primary fw-semibold btn-detail-klasifikasi"
                                                style="font-size: 0.75rem;"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalKlasifikasiPortofolio"
                                                data-id="{{ $item->id }}"
                                                data-nama="{{ $item->nama }}"
                                                data-kode="{{ $item->kode }}"
                                                data-bisa-kelola="1"
                                                data-item="{{ json_encode($item->kodeSurat) }}">
                                            Lihat &amp; Kelola ({{ $item->kodeSurat->count() }})
                                        </button>
                                @else
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="text-muted small fst-italic">Belum ada klasifikasi sampel</span>
                                        @if ($bisaKelolaPorto)
                                            <button type="button"
                                                    class="btn btn-outline-primary btn-sm py-0 px-2 rounded-2"
                                                    style="font-size: 0.72rem;"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalKlasifikasiPortofolio"
                                                    data-id="{{ $item->id }}"
                                                    data-nama="{{ $item->nama }}"
                                                    data-kode="{{ $item->kode }}"
                                                    data-bisa-kelola="1"
                                                    data-buka-form="1"
                                                    data-item="[]">
                                                + Tambah
                                            </button>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="text-center">{{ $item->retensi_tahun ? $item->retensi_tahun.' '.($item->retensi_satuan ?? 'bulan') : '-' }}</td>
                            <td class="text-center">
                                @if (!$bisaKelolaPorto)
                                    <span class="text-muted small fst-italic">Terkunci (Pusat)</span>
                                @else
                                    <div class="d-flex justify-content-center gap-2">
                                        <button type="button"
                                                class="btn btn-sm btn-outline-primary btn-edit-portofolio"
                                                data-action="{{ route('portofolio.update', $item) }}"
                                                data-id="{{ $item->id }}"
                                                data-kode="{{ $item->kode }}"
                                                data-nama="{{ $item->nama }}"
                                                data-retensi="{{ $item->retensi_tahun }}"
                                                data-satuan="{{ $item->retensi_satuan ?? 'bulan' }}">Edit</button>
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger btn-hapus-portofolio"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalHapusPortofolio"
                                                data-action="{{ route('portofolio.destroy', $item) }}"
                                                data-nama="{{ $item->nama }} ({{ $item->kode }})">
                                            Hapus
                                        </button>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted small">Tidak ada data portofolio.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($daftarPortofolio->hasPages())
            <div class="p-3 d-flex justify-content-center">
                {{ $daftarPortofolio->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

    <div class="modal fade" id="modalHapusPortofolio" tabindex="-1" aria-labelledby="modalHapusPortofolioLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-body p-4 text-center">
                    <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle bg-danger-subtle text-danger mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 6h18m-2 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                    </div>
                    <h5 class="fw-bold mb-2">Hapus Portofolio?</h5>
                    <p class="text-muted small mb-3">Portofolio <strong id="hapusNamaPortofolio" class="text-dark"></strong> akan dihapus permanen dari sistem.</p>

                    <form id="formHapusPortofolio" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-light rounded-3 w-50" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger rounded-3 w-50">Hapus</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalKlasifikasiPortofolio" tabindex="-1" aria-labelledby="modalKlasifikasiPortofolioLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-0 pb-0 pt-3 px-3">
                    <div>
                        <h5 class="modal-title fw-bold text-primary mb-1" id="modalKlasifikasiPortofolioLabel">Klasifikasi Sampel Portofolio</h5>
                        <p class="text-muted small mb-0" id="modalKlasifikasiSubtitle">Standarisasi kode klasifikasi sampel komoditas dan masa retensi arsip.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2" id="boxAksiHeaderKlasifikasi">
                        <span class="small fw-semibold text-secondary">Daftar Komoditas Sampel:</span>
                        <button type="button" class="btn btn-primary btn-sm rounded-3 py-1 px-2.5 fw-semibold" id="btnBukaFormKlasifikasi" style="font-size: 0.78rem;">
                            + Tambah Klasifikasi Baru
                        </button>
                    </div>

                    <div id="boxFormTambahKlasifikasi" class="mb-3 p-3 bg-light rounded-3 border d-none">
                        <h6 class="fw-bold text-primary mb-2" style="font-size: 0.88rem;">Tambah Klasifikasi Sampel untuk Portofolio Ini</h6>
                        <form id="formTambahKlasifikasi" method="POST">
                            @csrf
                            <div class="row g-2">
                                <div class="col-12 col-md-4">
                                    <label class="form-label small fw-semibold text-secondary mb-1">Kode Sampel <span class="text-danger">*</span></label>
                                    <input type="text" name="kode" class="form-control form-control-sm text-uppercase" placeholder="Contoh: MNR-BB" required maxlength="20">
                                </div>
                                <div class="col-12 col-md-8">
                                    <label class="form-label small fw-semibold text-secondary mb-1">Nama Klasifikasi / Komoditas <span class="text-danger">*</span></label>
                                    <input type="text" name="nama" class="form-control form-control-sm" placeholder="Contoh: Batubara (Coal Sampling)" required maxlength="255">
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label small fw-semibold text-secondary mb-1">Retensi Aktif (Thn) <span class="text-danger">*</span></label>
                                    <input type="number" name="retensi_aktif" class="form-control form-control-sm" value="1" min="1" max="50" required>
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label small fw-semibold text-secondary mb-1">Retensi Inaktif (Thn) <span class="text-danger">*</span></label>
                                    <input type="number" name="retensi_inaktif" class="form-control form-control-sm" value="2" min="0" max="50" required>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-semibold text-secondary mb-1">Keterangan (Opsional)</label>
                                    <input type="text" name="keterangan" class="form-control form-control-sm" placeholder="Catatan aturan sampel...">
                                </div>
                            </div>
                            <div class="d-flex justify-content-end gap-2 mt-2">
                                <button type="button" class="btn btn-sm btn-light border" id="btnBatalTambahKlasifikasi">Batal</button>
                                <button type="submit" class="btn btn-sm btn-primary px-3">Simpan Klasifikasi</button>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive rounded-3 border">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3 py-2 fw-semibold" style="width: 140px;">Kode Sampel</th>
                                    <th class="py-2 fw-semibold">Nama Komoditas / Klasifikasi</th>
                                    <th class="py-2 text-center fw-semibold" style="width: 110px;">Retensi Aktif</th>
                                    <th class="py-2 text-center fw-semibold" style="width: 110px;">Retensi Inaktif</th>
                                    <th class="py-2 fw-semibold">Keterangan</th>
                                    <th class="pe-3 py-2 text-center fw-semibold th-aksi-klasifikasi" style="width: 80px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyKlasifikasiPortofolio">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-3 px-3">
                    <button type="button" class="btn btn-secondary btn-sm rounded-3 px-3" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var btnTambah = document.getElementById('btn-tambah-portofolio');
                var formPortofolio = document.getElementById('form-portofolio');
                var tabelPortofolio = document.getElementById('tabel-portofolio-wrap');
                var btnKembali = document.getElementById('btn-kembali-portofolio');
                var judulForm = document.getElementById('judul-form-portofolio');
                var inputMethod = document.getElementById('portofolio-method');
                var inputEditId = document.getElementById('portofolio-edit-id');
                var inputKode = document.getElementById('portofolio-kode');
                var inputNama = document.getElementById('portofolio-nama');
                var inputRetensi = document.getElementById('portofolio-retensi');
                var selectSatuan = document.getElementById('portofolio-retensi-satuan');
                var btnSimpan = document.getElementById('btn-simpan-portofolio');

                function bukaFormTambah() {
                    formPortofolio.action = formPortofolio.getAttribute('data-url-tambah');
                    formPortofolio.classList.remove('d-none');
                    tabelPortofolio.classList.add('d-none');
                    judulForm.textContent = 'Form Tambah Portofolio';
                    btnSimpan.textContent = '+ Tambah';
                    inputMethod.disabled = true;
                    inputEditId.value = '';
                    inputKode.value = '';
                    inputNama.value = '';
                    inputRetensi.value = '';
                    selectSatuan.value = 'bulan';
                    btnTambah.classList.add('d-none');
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }

                function bukaFormEdit(tombol) {
                    formPortofolio.action = tombol.getAttribute('data-action');
                    formPortofolio.classList.remove('d-none');
                    tabelPortofolio.classList.add('d-none');
                    judulForm.textContent = 'Form Edit Portofolio';
                    btnSimpan.textContent = 'Simpan Perubahan';
                    inputMethod.disabled = false;
                    inputEditId.value = tombol.getAttribute('data-id');
                    inputKode.value = tombol.getAttribute('data-kode');
                    inputNama.value = tombol.getAttribute('data-nama');
                    inputRetensi.value = tombol.getAttribute('data-retensi') || '';
                    selectSatuan.value = tombol.getAttribute('data-satuan') || 'bulan';
                    btnTambah.classList.add('d-none');
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }

                function tutupForm() {
                    formPortofolio.classList.add('d-none');
                    tabelPortofolio.classList.remove('d-none');
                    btnTambah.classList.remove('d-none');
                }

                if (btnTambah) {
                    btnTambah.addEventListener('click', bukaFormTambah);
                }
                if (btnKembali) {
                    btnKembali.addEventListener('click', tutupForm);
                }

                document.querySelectorAll('.btn-edit-portofolio').forEach(function (button) {
                    button.addEventListener('click', function () {
                        bukaFormEdit(this);
                    });
                });

                @if ($errors->portofolio->any())
                    formPortofolio.classList.remove('d-none');
                    tabelPortofolio.classList.add('d-none');
                    btnTambah.classList.add('d-none');
                @endif

                var modalHapus = document.getElementById('modalHapusPortofolio');
                if (modalHapus) {
                    modalHapus.addEventListener('show.bs.modal', function (event) {
                        var btn = event.relatedTarget;
                        var action = btn.getAttribute('data-action');
                        var nama = btn.getAttribute('data-nama');
                        document.getElementById('formHapusPortofolio').action = action;
                        document.getElementById('hapusNamaPortofolio').textContent = nama;
                    });
                }

                                                var modalKlasifikasi = document.getElementById('modalKlasifikasiPortofolio');
                var btnBukaFormKlasifikasi = document.getElementById('btnBukaFormKlasifikasi');
                var boxFormTambahKlasifikasi = document.getElementById('boxFormTambahKlasifikasi');
                var btnBatalTambahKlasifikasi = document.getElementById('btnBatalTambahKlasifikasi');
                var formTambahKlasifikasi = document.getElementById('formTambahKlasifikasi');
                var boxAksiHeaderKlasifikasi = document.getElementById('boxAksiHeaderKlasifikasi');

                if (btnBukaFormKlasifikasi && boxFormTambahKlasifikasi) {
                    btnBukaFormKlasifikasi.addEventListener('click', function () {
                        boxFormTambahKlasifikasi.classList.remove('d-none');
                        btnBukaFormKlasifikasi.classList.add('d-none');
                    });
                }

                if (btnBatalTambahKlasifikasi && boxFormTambahKlasifikasi) {
                    btnBatalTambahKlasifikasi.addEventListener('click', function () {
                        boxFormTambahKlasifikasi.classList.add('d-none');
                        btnBukaFormKlasifikasi.classList.remove('d-none');
                    });
                }

                if (modalKlasifikasi) {
                    modalKlasifikasi.addEventListener('show.bs.modal', function (event) {
                        var btn = event.relatedTarget;
                        if (!btn) return;

                        var portofolioId = btn.getAttribute('data-id');
                        var nama = btn.getAttribute('data-nama');
                        var kode = btn.getAttribute('data-kode');
                        var bukaFormOtomatis = btn.getAttribute('data-buka-form') === '1';
                        var items = JSON.parse(btn.getAttribute('data-item') || '[]');

                        document.getElementById('modalKlasifikasiPortofolioLabel').textContent = 'Klasifikasi Sampel - ' + nama + ' (' + kode + ')';
                        var subtitleEl = document.getElementById('modalKlasifikasiSubtitle');
                        subtitleEl.textContent = 'Kelola standarisasi komoditas dan klasifikasi sampel untuk portofolio ini.';

                        formTambahKlasifikasi.action = '/portofolio/' + portofolioId + '/klasifikasi';

                        if (boxAksiHeaderKlasifikasi) boxAksiHeaderKlasifikasi.classList.remove('d-none');
                        var thAksi = document.querySelector('#modalKlasifikasiPortofolio .th-aksi-klasifikasi');
                        if (thAksi) thAksi.classList.remove('d-none');

                        if (bukaFormOtomatis || items.length === 0) {
                            boxFormTambahKlasifikasi.classList.remove('d-none');
                            btnBukaFormKlasifikasi.classList.add('d-none');
                        } else {
                            boxFormTambahKlasifikasi.classList.add('d-none');
                            if (btnBukaFormKlasifikasi) btnBukaFormKlasifikasi.classList.remove('d-none');
                        }

                        var tbody = document.getElementById('tbodyKlasifikasiPortofolio');
                        tbody.innerHTML = '';

                        if (items.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-3 text-muted">Belum ada klasifikasi sampel untuk portofolio ini. Silakan klik tombol "+ Tambah Klasifikasi Baru" di atas.</td></tr>';
                            return;
                        }

                        var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

                        items.forEach(function (ks) {
                            var tr = document.createElement('tr');
                            var badgeAsal = !ks.cabang_id
                                ? '<span class="badge bg-primary-subtle text-primary border ms-1" style="font-size: 0.68rem;">Pusat</span>'
                                : '<span class="badge bg-success-subtle text-success border ms-1" style="font-size: 0.68rem;">Cabang</span>';

                            var kolomAksi = '';
                            if (!ks.cabang_id) {
                                kolomAksi = `<td class="pe-3 py-2 text-center text-muted small fst-italic">Terkunci</td>`;
                            } else {
                                kolomAksi = `
                                    <td class="pe-3 py-2 text-center">
                                        <form method="POST" action="/portofolio/klasifikasi/${ks.id}" onsubmit="return confirm('Hapus klasifikasi ${ks.kode}?')">
                                            <input type="hidden" name="_token" value="${csrfToken}">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="btn btn-outline-danger btn-sm p-1 rounded-2" style="width: 28px; height: 28px;" title="Hapus">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                            </button>
                                        </form>
                                    </td>
                                `;
                            }

                            tr.innerHTML = `
                                <td class="ps-3 fw-bold font-monospace text-primary">${ks.kode}</td>
                                <td class="fw-semibold text-dark">${ks.nama} ${badgeAsal}</td>
                                <td class="text-center"><span class="badge bg-light text-dark border">${ks.retensi_aktif} Tahun</span></td>
                                <td class="text-center"><span class="badge bg-light text-secondary border">${ks.retensi_inaktif} Tahun</span></td>
                                <td class="text-muted small">${ks.keterangan || '-'}</td>
                                ${kolomAksi}
                            `;
                            tbody.appendChild(tr);
                        });
                    });
                }
            });
        </script>
    @endpush
</x-admin.layout>