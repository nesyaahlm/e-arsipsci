<x-admin.layout title="Bagian">
    <h1 class="h3 fw-bold text-primary mb-1">Bagian</h1>
    <p class="text-primary mb-4">Bagian atau kategori untuk pengelompokan dokumen maupun sampel.</p>

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

    {{-- Form Pencarian & Tombol Tambah --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <form action="{{ route('bagian.index') }}" method="GET" class="d-flex gap-2 flex-grow-1" style="max-width: 400px;">
            <input type="text" name="cari" value="{{ $cari ?? '' }}" class="form-control form-control-sm" placeholder="Cari kode, nama, atau retensi...">
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
                    <label for="bagian-retensi" class="form-label">Retensi Bagian (tahun, kosong = selama berlaku)</label>
                    <input type="number" id="bagian-retensi" name="retensi_tahun" min="1" max="100"
                           class="form-control {{ $errors->bagian->has('retensi_tahun') ? 'is-invalid' : '' }}"
                           value="{{ $errors->bagian->any() ? old('retensi_tahun') : '' }}">
                    <div class="invalid-feedback">{{ $errors->bagian->first('retensi_tahun') }}</div>
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
                        <th class="text-center" style="width: 60px;">No</th>
                        <th class="text-center">Kode Bagian</th>
                        <th class="text-center">Nama Bagian</th>
                        <th class="text-center">Retensi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($daftarBagian as $index => $bagian)
                        <tr>
                            <td class="text-center">{{ $daftarBagian->firstItem() + $index }}</td>
                            <td class="fw-bold text-center">{{ $bagian->kode }}</td>
                            <td class="text-start">{{ $bagian->nama }}</td>
                            <td class="text-center">{{ $bagian->retensi_tahun ? $bagian->retensi_tahun.' tahun' : '-' }}</td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary btn-edit-bagian"
                                            data-action="{{ route('bagian.update', $bagian) }}"
                                            data-id="{{ $bagian->id }}"
                                            data-kode="{{ $bagian->kode }}"
                                            data-nama="{{ $bagian->nama }}"
                                            data-retensi="{{ $bagian->retensi_tahun }}">Edit</button>
                                    <form method="POST"
                                          action="{{ route('bagian.destroy', $bagian) }}"
                                          data-konfirmasi="Hapus bagian {{ $bagian->nama }}?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-secondary py-4">Belum ada data bagian yang ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination (Dipindah ke pojok kanan) --}}
    @if ($daftarBagian->hasPages())
        <div class="mt-3 d-flex justify-content-end">
            {{ $daftarBagian->links('pagination::bootstrap-5') }}
        </div>
    @endif

    @push('scripts')
    <script>
        const tombolTambah = document.getElementById('btn-tambah-bagian');
        const tombolKembali = document.getElementById('btn-kembali-bagian');
        const tombolSimpan = document.getElementById('btn-simpan-bagian');
        const formBagian = document.getElementById('form-bagian');
        const tabelBagian = document.getElementById('tabel-bagian-wrap');
        const judulForm = document.getElementById('judul-form-bagian');
        const metodeBagian = document.getElementById('bagian-method');
        const idBagian = document.getElementById('bagian-edit-id');

        function kosongkanForm() {
            formBagian.querySelectorAll('input[type="text"], input[type="number"]').forEach(function (el) {
                el.value = '';
            });
            formBagian.querySelectorAll('.is-invalid').forEach(function (el) {
                el.classList.remove('is-invalid');
            });
        }

        function modeTambah() {
            kosongkanForm();
            formBagian.action = formBagian.dataset.urlTambah;
            metodeBagian.disabled = true;
            idBagian.value = '';
            judulForm.textContent = 'Form Tambah Bagian';
            tombolSimpan.textContent = '+ Tambah';
        }

        function modeEdit(tombol) {
            kosongkanForm();
            formBagian.action = tombol.dataset.action;
            metodeBagian.disabled = false;
            idBagian.value = tombol.dataset.id;
            judulForm.textContent = 'Form Edit Bagian';
            tombolSimpan.textContent = 'Simpan Perubahan';
            document.getElementById('bagian-kode').value = tombol.dataset.kode;
            document.getElementById('bagian-nama').value = tombol.dataset.nama;
            document.getElementById('bagian-retensi').value = tombol.dataset.retensi || '';
        }

        function bukaForm() {
            formBagian.classList.remove('d-none');
            tabelBagian.classList.add('d-none');
            tombolTambah.disabled = true;
            document.getElementById('bagian-kode').focus();
        }

        function tutupForm() {
            modeTambah();
            formBagian.classList.add('d-none');
            tabelBagian.classList.remove('d-none');
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

        document.querySelectorAll('.btn-edit-bagian').forEach(function (tombol) {
            tombol.addEventListener('click', function () {
                modeEdit(tombol);
                bukaForm();
            });
        });

        @if ($errors->bagian->any())
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