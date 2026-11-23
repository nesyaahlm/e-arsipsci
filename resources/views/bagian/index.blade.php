<x-admin.layout title="Bagian">
    <h1 class="h3 fw-bold text-primary mb-1">Bagian &amp; Klasifikasi Dokumen</h1>
    <p class="text-primary mb-4">Daftar bagian/divisi kerja beserta standarisasi kode klasifikasi dokumen kearsipan.</p>

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
        <form action="{{ route('bagian.index') }}" method="GET" class="d-flex gap-2 flex-grow-1" style="max-width: 420px;">
            <input type="text" name="cari" value="{{ $cari ?? '' }}" class="form-control form-control-sm" placeholder="Cari kode bagian, nama, atau klasifikasi surat...">
            <button type="submit" class="btn btn-sm btn-primary">Cari</button>
            @if(request('cari'))
                <a href="{{ route('bagian.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            @endif
        </form>

        <button type="button" id="btn-tambah-bagian" class="btn btn-sm btn-tambah-layanan">+ Tambah Bagian</button>
    </div>

    <div class="panel-layanan" style="border-top-left-radius: 14px;">
        @php
            $modeEdit = $errors->bagian->any() && old('edit_id');
        @endphp
        <form method="POST"
              action="{{ $modeEdit ? route('bagian.update', old('edit_id')) : route('bagian.store') }}"
              data-url-tambah="{{ route('bagian.store') }}"
              id="form-bagian"
              class="form-jenis d-none">
            @csrf
            <input type="hidden" name="_method" value="PUT" id="bagian-method" @disabled(! $modeEdit)>
            <input type="hidden" name="edit_id" id="bagian-edit-id" value="{{ $modeEdit ? old('edit_id') : '' }}">
            <h2 class="form-jenis-judul" id="judul-form-bagian">{{ $modeEdit ? 'Form Edit Bagian' : 'Form Tambah Bagian' }}</h2>

            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label for="bagian-kode" class="form-label">Kode Bagian</label>
                    <input type="text" id="bagian-kode" name="kode" maxlength="10" required
                           class="form-control text-uppercase {{ $errors->bagian->has('kode') ? 'is-invalid' : '' }}"
                           value="{{ $errors->bagian->any() ? old('kode') : '' }}">
                    <div class="invalid-feedback">{{ $errors->bagian->first('kode') }}</div>
                </div>
                <div class="col-12 col-md-6">
                    <label for="bagian-nama" class="form-label">Nama Bagian</label>
                    <input type="text" id="bagian-nama" name="nama" maxlength="255" required
                           class="form-control {{ $errors->bagian->has('nama') ? 'is-invalid' : '' }}"
                           value="{{ $errors->bagian->any() ? old('nama') : '' }}">
                    <div class="invalid-feedback">{{ $errors->bagian->first('nama') }}</div>
                </div>
                <div class="col-12 col-md-6">
                    <label for="bagian-retensi" class="form-label">Retensi Bagian (kosong = selama berlaku)</label>
                    <div class="input-group">
                        <input type="number" id="bagian-retensi" name="retensi_tahun" min="1" max="100"
                               class="form-control {{ $errors->bagian->has('retensi_tahun') ? 'is-invalid' : '' }}"
                               style="border: 1px solid #2a7f72; border-top-right-radius: 0; border-bottom-right-radius: 0;"
                               value="{{ $errors->bagian->any() ? old('retensi_tahun') : '' }}">
                        <select id="bagian-retensi-satuan" name="retensi_satuan" class="form-select"
                                style="max-width: 110px; border: 1px solid #2a7f72; border-left: none; border-top-left-radius: 0; border-bottom-left-radius: 0; border-top-right-radius: 8px; border-bottom-right-radius: 8px;">
                            <option value="tahun" {{ old('retensi_satuan') === 'tahun' ? 'selected' : '' }}>Tahun</option>
                            <option value="bulan" {{ old('retensi_satuan') === 'bulan' ? 'selected' : '' }}>Bulan</option>
                        </select>
                    </div>
                    <div class="invalid-feedback d-block">{{ $errors->bagian->first('retensi_tahun') }}</div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="submit" id="btn-simpan-bagian" class="btn-simpan">{{ $modeEdit ? 'Simpan Perubahan' : '+ Tambah' }}</button>
                <button type="button" id="btn-kembali-bagian" class="btn-batal">Kembali</button>
            </div>
        </form>

        <div class="table-responsive" id="tabel-bagian-wrap">
            <table class="table table-hover align-middle mb-0 tabel-jenis">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">No</th>
                        <th class="text-center" style="width: 90px;">Kode</th>
                        <th class="text-start" style="width: 230px;">Nama Bagian</th>
                        <th class="text-start">Klasifikasi Dokumen (Kode Surat)</th>
                        <th class="text-center" style="width: 100px;">Retensi</th>
                        <th class="text-center" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($daftarBagian as $index => $bagian)
                        @php
                            $bisaKelola = !is_null($bagian->cabang_id);
                        @endphp
                        <tr>
                            <td class="text-center">{{ $daftarBagian->firstItem() + $index }}</td>
                            <td class="fw-bold text-center">
                                <span class="badge rounded-pill px-2.5 py-1 font-monospace" style="background-color: #e6f4f1; color: #277a72; font-size: 0.82rem;">
                                    {{ $bagian->kode }}
                                </span>
                            </td>
                            <td class="text-start">
                                @if ($bagian->kodeSurat && $bagian->kodeSurat->isNotEmpty())
                                    <div class="d-flex flex-wrap gap-1 align-items-center">
                                        @foreach ($bagian->kodeSurat as $ks)
                                            <span class="badge bg-light text-dark border px-2 py-1 font-monospace" style="font-size: 0.75rem;" title="{{ $ks->nama }} (Retensi Aktif: {{ $ks->retensi_aktif }} Thn)">
                                                {{ $ks->kode }}
                                            </span>
                                        @endforeach
                                        <button type="button"
                                                class="btn btn-link btn-sm p-0 ms-1 text-decoration-none text-primary fw-semibold btn-detail-klasifikasi"
                                                style="font-size: 0.75rem;"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalKlasifikasiBagian"
                                                data-id="{{ $bagian->id }}"
                                                data-nama="{{ $bagian->nama }}"
                                                data-kode="{{ $bagian->kode }}"
                                                data-bisa-kelola="1"
                                                data-item="{{ json_encode($bagian->kodeSurat) }}">
                                            Lihat &amp; Kelola ({{ $bagian->kodeSurat->count() }})
                                        </button>
                                    </div>
                                @else
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="text-muted small fst-italic">Belum ada klasifikasi surat</span>
                                        <button type="button"
                                                class="btn btn-outline-primary btn-sm py-0 px-2 rounded-2 btn-tambah-langsung"
                                                style="font-size: 0.72rem;"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalKlasifikasiBagian"
                                                data-id="{{ $bagian->id }}"
                                                data-nama="{{ $bagian->nama }}"
                                                data-kode="{{ $bagian->kode }}"
                                                data-bisa-kelola="1"
                                                data-buka-form="1"
                                                data-item="[]">
                                            + Tambah
                                        </button>
                                    </div>
                                @endif
                            </td>
                            <td class="text-center">{{ $bagian->retensi_tahun ? $bagian->retensi_tahun.' '.($bagian->retensi_satuan ?? 'tahun') : '-' }}</td>
                            <td class="text-center">
                                @if (!$bisaKelola)
                                    <span class="text-muted small fst-italic">Terkunci (Pusat)</span>
                                @else
                                    <div class="d-flex justify-content-center gap-2">
                                        <button type="button"
                                                class="btn btn-sm btn-outline-primary btn-edit-bagian"
                                                data-action="{{ route('bagian.update', $bagian) }}"
                                                data-id="{{ $bagian->id }}"
                                                data-kode="{{ $bagian->kode }}"
                                                data-nama="{{ $bagian->nama }}"
                                                data-retensi="{{ $bagian->retensi_tahun }}"
                                                data-satuan="{{ $bagian->retensi_satuan ?? 'tahun' }}">Edit</button>
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger btn-hapus-bagian"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalHapusBagian"
                                                data-action="{{ route('bagian.destroy', $bagian) }}"
                                                data-nama="{{ $bagian->nama }} ({{ $bagian->kode }})">
                                            Hapus
                                        </button>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted small">Tidak ada data bagian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($daftarBagian->hasPages())
            <div class="p-3 d-flex justify-content-center">
                {{ $daftarBagian->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

    <div class="modal fade" id="modalHapusBagian" tabindex="-1" aria-labelledby="modalHapusBagianLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-body p-4 text-center">
                    <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle bg-danger-subtle text-danger mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 6h18m-2 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                    </div>
                    <h5 class="fw-bold mb-2">Hapus Bagian?</h5>
                    <p class="text-muted small mb-3">Bagian <strong id="hapusNamaBagian" class="text-dark"></strong> akan dihapus permanen dari sistem.</p>

                    <form id="formHapusBagian" method="POST">
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

    <div class="modal fade" id="modalKlasifikasiBagian" tabindex="-1" aria-labelledby="modalKlasifikasiBagianLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-0 pb-0 pt-3 px-3">
                    <div>
                        <h5 class="modal-title fw-bold text-primary mb-1" id="modalKlasifikasiBagianLabel">Klasifikasi Dokumen Bagian</h5>
                        <p class="text-muted small mb-0" id="modalKlasifikasiSubtitle">Standarisasi kode surat dinas dan masa retensi arsip.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2" id="boxAksiHeaderKlasifikasi">
                        <span class="small fw-semibold text-secondary">Daftar Kode Surat:</span>
                        <button type="button" class="btn btn-primary btn-sm rounded-3 py-1 px-2.5 fw-semibold" id="btnBukaFormKlasifikasi" style="font-size: 0.78rem;">
                            + Tambah Klasifikasi Baru
                        </button>
                    </div>

                    <div id="boxFormTambahKlasifikasi" class="mb-3 p-3 bg-light rounded-3 border d-none">
                        <h6 class="fw-bold text-primary mb-2" style="font-size: 0.88rem;">Tambah Klasifikasi Baru untuk Bagian Ini</h6>
                        <form id="formTambahKlasifikasi" method="POST">
                            @csrf
                            <div class="row g-2">
                                <div class="col-12 col-md-4">
                                    <label class="form-label small fw-semibold text-secondary mb-1">Kode Surat <span class="text-danger">*</span></label>
                                    <input type="text" name="kode" class="form-control form-control-sm text-uppercase" placeholder="Contoh: IMP-LAP" required maxlength="20">
                                </div>
                                <div class="col-12 col-md-8">
                                    <label class="form-label small fw-semibold text-secondary mb-1">Nama Klasifikasi <span class="text-danger">*</span></label>
                                    <input type="text" name="nama" class="form-control form-control-sm" placeholder="Contoh: Laporan Operasional IMUP" required maxlength="255">
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label small fw-semibold text-secondary mb-1">Retensi Aktif (Thn) <span class="text-danger">*</span></label>
                                    <input type="number" name="retensi_aktif" class="form-control form-control-sm" value="2" min="1" max="50" required>
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label small fw-semibold text-secondary mb-1">Retensi Inaktif (Thn) <span class="text-danger">*</span></label>
                                    <input type="number" name="retensi_inaktif" class="form-control form-control-sm" value="5" min="0" max="50" required>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-semibold text-secondary mb-1">Keterangan (Opsional)</label>
                                    <input type="text" name="keterangan" class="form-control form-control-sm" placeholder="Catatan aturan kearsipan...">
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
                                    <th class="ps-3 py-2 fw-semibold" style="width: 140px;">Kode Surat</th>
                                    <th class="py-2 fw-semibold">Nama Klasifikasi Surat</th>
                                    <th class="py-2 text-center fw-semibold" style="width: 110px;">Retensi Aktif</th>
                                    <th class="py-2 text-center fw-semibold" style="width: 110px;">Retensi Inaktif</th>
                                    <th class="py-2 fw-semibold">Keterangan</th>
                                    <th class="pe-3 py-2 text-center fw-semibold th-aksi-klasifikasi" style="width: 80px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyKlasifikasiBagian">
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
                var btnTambah = document.getElementById('btn-tambah-bagian');
                var formBagian = document.getElementById('form-bagian');
                var tabelBagian = document.getElementById('tabel-bagian-wrap');
                var btnKembali = document.getElementById('btn-kembali-bagian');
                var judulForm = document.getElementById('judul-form-bagian');
                var inputMethod = document.getElementById('bagian-method');
                var inputEditId = document.getElementById('bagian-edit-id');
                var inputKode = document.getElementById('bagian-kode');
                var inputNama = document.getElementById('bagian-nama');
                var inputRetensi = document.getElementById('bagian-retensi');
                var selectSatuan = document.getElementById('bagian-retensi-satuan');
                var btnSimpan = document.getElementById('btn-simpan-bagian');

                function bukaFormTambah() {
                    formBagian.action = formBagian.getAttribute('data-url-tambah');
                    formBagian.classList.remove('d-none');
                    tabelBagian.classList.add('d-none');
                    judulForm.textContent = 'Form Tambah Bagian';
                    btnSimpan.textContent = '+ Tambah';
                    inputMethod.disabled = true;
                    inputEditId.value = '';
                    inputKode.value = '';
                    inputNama.value = '';
                    inputRetensi.value = '';
                    selectSatuan.value = 'tahun';
                    btnTambah.classList.add('d-none');
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }

                function bukaFormEdit(tombol) {
                    formBagian.action = tombol.getAttribute('data-action');
                    formBagian.classList.remove('d-none');
                    tabelBagian.classList.add('d-none');
                    judulForm.textContent = 'Form Edit Bagian';
                    btnSimpan.textContent = 'Simpan Perubahan';
                    inputMethod.disabled = false;
                    inputEditId.value = tombol.getAttribute('data-id');
                    inputKode.value = tombol.getAttribute('data-kode');
                    inputNama.value = tombol.getAttribute('data-nama');
                    inputRetensi.value = tombol.getAttribute('data-retensi') || '';
                    selectSatuan.value = tombol.getAttribute('data-satuan') || 'tahun';
                    btnTambah.classList.add('d-none');
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }

                function tutupForm() {
                    formBagian.classList.add('d-none');
                    tabelBagian.classList.remove('d-none');
                    btnTambah.classList.remove('d-none');
                }

                if (btnTambah) {
                    btnTambah.addEventListener('click', bukaFormTambah);
                }
                if (btnKembali) {
                    btnKembali.addEventListener('click', tutupForm);
                }

                document.querySelectorAll('.btn-edit-bagian').forEach(function (button) {
                    button.addEventListener('click', function () {
                        bukaFormEdit(this);
                    });
                });

                @if ($errors->bagian->any())
                    formBagian.classList.remove('d-none');
                    tabelBagian.classList.add('d-none');
                    btnTambah.classList.add('d-none');
                @endif

                var modalHapus = document.getElementById('modalHapusBagian');
                if (modalHapus) {
                    modalHapus.addEventListener('show.bs.modal', function (event) {
                        var btn = event.relatedTarget;
                        var action = btn.getAttribute('data-action');
                        var nama = btn.getAttribute('data-nama');
                        document.getElementById('formHapusBagian').action = action;
                        document.getElementById('hapusNamaBagian').textContent = nama;
                    });
                }

                var modalKlasifikasi = document.getElementById('modalKlasifikasiBagian');
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

                        var bagianId = btn.getAttribute('data-id');
                        var nama = btn.getAttribute('data-nama');
                        var kode = btn.getAttribute('data-kode');
                        var bukaFormOtomatis = btn.getAttribute('data-buka-form') === '1';
                        var items = JSON.parse(btn.getAttribute('data-item') || '[]');

                        document.getElementById('modalKlasifikasiBagianLabel').textContent = 'Klasifikasi Dokumen - ' + nama + ' (' + kode + ')';
                        var subtitleEl = document.getElementById('modalKlasifikasiSubtitle');
                        subtitleEl.textContent = 'Kelola standarisasi kode surat dinas dan masa retensi arsip.';

                        formTambahKlasifikasi.action = '/bagian/' + bagianId + '/klasifikasi';

                        if (boxAksiHeaderKlasifikasi) boxAksiHeaderKlasifikasi.classList.remove('d-none');
                        var thAksi = document.querySelector('#modalKlasifikasiBagian .th-aksi-klasifikasi');
                        if (thAksi) thAksi.classList.remove('d-none');

                        if (bukaFormOtomatis || items.length === 0) {
                            boxFormTambahKlasifikasi.classList.remove('d-none');
                            btnBukaFormKlasifikasi.classList.add('d-none');
                        } else {
                            boxFormTambahKlasifikasi.classList.add('d-none');
                            if (btnBukaFormKlasifikasi) btnBukaFormKlasifikasi.classList.remove('d-none');
                        }

                        var tbody = document.getElementById('tbodyKlasifikasiBagian');
                        tbody.innerHTML = '';

                        if (items.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-3 text-muted">Belum ada klasifikasi surat untuk bagian ini. Silakan klik tombol "+ Tambah Klasifikasi Baru" di atas.</td></tr>';
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
                                        <form method="POST" action="/bagian/klasifikasi/${ks.id}" onsubmit="return confirm('Hapus klasifikasi ${ks.kode}?')">
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