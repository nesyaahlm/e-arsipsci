<x-admin.layout title="Portofolio Sampel">
    <h1 class="h3 fw-bold text-primary mb-1">Portofolio Sampel</h1>
    <p class="text-primary mb-4">Portofolio atau kategori komoditas untuk pengelompokan arsip sampel laboratorium.</p>

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

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <form action="{{ route('portofolio.index') }}" method="GET" class="d-flex gap-2 flex-grow-1" style="max-width: 400px;">
            <input type="text" name="cari" value="{{ $cari ?? '' }}" class="form-control form-control-sm" placeholder="Cari kode atau nama portofolio...">
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
                            <option value="bulan" {{ old('retensi_satuan', 'bulan') === 'bulan' ? 'selected' : '' }}>Bulan</option>
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
                        <th class="text-center" style="width: 60px;">No</th>
                        <th class="text-center">Kode Portofolio</th>
                        <th class="text-center">Nama Portofolio / Komoditas</th>
                        <th class="text-center">Retensi Default</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($daftarPortofolio as $index => $item)
                        <tr>
                            <td class="text-center">{{ $daftarPortofolio->firstItem() + $index }}</td>
                            <td class="fw-bold text-center">{{ $item->kode }}</td>
                            <td class="text-start">
                                <div>{{ $item->nama }}</div>
                                @if (is_null($item->cabang_id))
                                    <span class="badge bg-primary-subtle text-primary border" style="font-size: 0.7rem;">Standar Nasional</span>
                                @else
                                    <span class="badge bg-success-subtle text-success border" style="font-size: 0.7rem;">Khusus Unit Kerja</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $item->retensi_tahun ? $item->retensi_tahun.' '.($item->retensi_satuan ?? 'bulan') : '-' }}</td>
                            <td class="text-center">
                                @if (is_null($item->cabang_id))
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
                                        <form method="POST"
                                              action="{{ route('portofolio.destroy', $item) }}"
                                              data-konfirmasi="Hapus portofolio {{ $item->nama }}?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                        </form>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-secondary py-4">Belum ada data portofolio sampel yang ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($daftarPortofolio->hasPages())
        <div class="mt-3 d-flex justify-content-end">
            {{ $daftarPortofolio->links('pagination::bootstrap-5') }}
        </div>
    @endif

    @push('scripts')
    <script>
        const tombolTambah = document.getElementById('btn-tambah-portofolio');
        const tombolKembali = document.getElementById('btn-kembali-portofolio');
        const tombolSimpan = document.getElementById('btn-simpan-portofolio');
        const formPortofolio = document.getElementById('form-portofolio');
        const tabelPortofolio = document.getElementById('tabel-portofolio-wrap');
        const judulForm = document.getElementById('judul-form-portofolio');
        const metodePortofolio = document.getElementById('portofolio-method');
        const idPortofolio = document.getElementById('portofolio-edit-id');

        function kosongkanForm() {
            formPortofolio.querySelectorAll('input[type="text"], input[type="number"]').forEach(function (el) {
                el.value = '';
            });
            formPortofolio.querySelectorAll('.is-invalid').forEach(function (el) {
                el.classList.remove('is-invalid');
            });
            document.getElementById('portofolio-retensi-satuan').value = 'bulan';
        }

        function modeTambah() {
            kosongkanForm();
            formPortofolio.action = formPortofolio.dataset.urlTambah;
            metodePortofolio.disabled = true;
            idPortofolio.value = '';
            judulForm.textContent = 'Form Tambah Portofolio';
            tombolSimpan.textContent = '+ Tambah';
        }

        function modeEdit(tombol) {
            kosongkanForm();
            formPortofolio.action = tombol.dataset.action;
            metodePortofolio.disabled = false;
            idPortofolio.value = tombol.dataset.id;
            judulForm.textContent = 'Form Edit Portofolio';
            tombolSimpan.textContent = 'Simpan Perubahan';
            document.getElementById('portofolio-kode').value = tombol.dataset.kode;
            document.getElementById('portofolio-nama').value = tombol.dataset.nama;
            document.getElementById('portofolio-retensi').value = tombol.dataset.retensi || '';
            document.getElementById('portofolio-retensi-satuan').value = tombol.dataset.satuan || 'bulan';
        }

        function bukaForm() {
            formPortofolio.classList.remove('d-none');
            tabelPortofolio.classList.add('d-none');
            tombolTambah.disabled = true;
            document.getElementById('portofolio-kode').focus();
        }

        function tutupForm() {
            modeTambah();
            formPortofolio.classList.add('d-none');
            tabelPortofolio.classList.remove('d-none');
            tombolTambah.disabled = false;
        }

        if(tombolTambah) {
            tombolTambah.addEventListener('click', function () {
                modeTambah();
                bukaForm();
            });
        }

        if(tombolKembali) {
            tombolKembali.addEventListener('click', tutupForm);
        }

        document.querySelectorAll('.btn-edit-portofolio').forEach(function (tombol) {
            tombol.addEventListener('click', function () {
                modeEdit(tombol);
                bukaForm();
            });
        });

        @if ($errors->portofolio->any())
            bukaForm();
        @endif

        document.querySelectorAll('.alert-otomatis').forEach(function (el) {
            setTimeout(function () {
                if (el.isConnected) {
                    bootstrap.Alert.getOrCreateInstance(el).close();
                }
            }, 5000);
        });
    </script>
    @endpush
</x-admin.layout>