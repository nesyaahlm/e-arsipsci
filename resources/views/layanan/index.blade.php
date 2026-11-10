<x-admin.layout title="Layanan & Jenis">

    <h1 class="h3 fw-bold text-primary mb-1">Layanan &amp; Jenis</h1>
    <p class="text-primary mb-4">Layanan adalah bidang usaha. Jenis dokumen menentukan kode nomor dan retensi.</p>
    
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
    
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-2">
        <ul class="nav tab-layanan" role="tablist">
            <li class="nav-item" role="presentation">
                <button type="button" class="nav-link active" data-bs-toggle="tab" data-bs-target="#panel-layanan" role="tab">LAYANAN</button>
            </li>
            <li class="nav-item" role="presentation">
                <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#panel-jenis" role="tab">JENIS</button>
            </li>
        </ul>
        @if ($bisaKelola)
            <div class="d-flex gap-2 pb-2">
                <button type="button" id="btn-tambah-layanan" class="btn btn-sm btn-tambah-layanan" data-bs-toggle="modal" data-bs-target="#modal-tambah">+ Tambah Layanan</button>
                <button type="button" id="btn-tambah-jenis" class="btn btn-sm btn-tambah-jenis" disabled>+ Tambah Jenis</button>
            </div>
        @endif
    </div>
    
    <div class="tab-content panel-layanan">
        <div class="tab-pane fade show active" id="panel-layanan" role="tabpanel">
            <div class="row g-4 justify-content-center">
                @forelse ($daftarLayanan as $layanan)
                    <div class="col-12 col-md-6 col-xl-4">
                        <article class="card kartu-layanan h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h3 class="fw-bold text-uppercase text-primary mb-0" style="font-size: 0.85rem;">{{ $layanan->nama }}</h3>
                                        <p class="small text-secondary mb-0">{{ $layanan->jenisDokumen->count() }} jenis dokumen</p>
                                    </div>
                                    <span class="kartu-ikon text-primary">@include('layanan._ikon', ['nama' => $layanan->nama])</span>
                                </div>
    
                                @if ($layanan->deskripsi)
                                    <p class="deskripsi-layanan mt-2 mb-0" title="{{ $layanan->deskripsi }}">{{ $layanan->deskripsi }}</p>
                                @endif
    
                                <div class="d-flex flex-column align-items-start gap-1 mt-3">
                                    @forelse ($layanan->jenisDokumen as $jenis)
                                        <span class="tag-jenis">{{ $jenis->nama }}</span>
                                    @empty
                                        <span class="small text-secondary fst-italic">Belum ada jenis dokumen</span>
                                    @endforelse
                                </div>
                            </div>
    
                            @if ($bisaKelola)
                                <div class="card-footer bg-white d-flex gap-2">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modal-edit"
                                            data-action="{{ route('layanan.update', $layanan) }}"
                                            data-id="{{ $layanan->id }}"
                                            data-nama="{{ $layanan->nama }}"
                                            data-deskripsi="{{ $layanan->deskripsi }}">Edit</button>
                                    <form method="POST"
                                          action="{{ route('layanan.destroy', $layanan) }}"
                                          data-konfirmasi="Hapus layanan {{ $layanan->nama }}?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                    </form>
                                </div>
                            @endif
                        </article>
                    </div>
                @empty
                    <p class="text-center text-secondary py-4">Belum ada layanan.</p>
                @endforelse
            </div>
        </div>
    
        <div class="tab-pane fade" id="panel-jenis" role="tabpanel">
            @if ($bisaKelola)
                @php
                    $modeEdit = $errors->jenis->any() && old('edit_id');
                @endphp
                <form method="POST"
                      action="{{ $modeEdit ? route('jenis.update', old('edit_id')) : route('jenis.store') }}"
                      data-url-tambah="{{ route('jenis.store') }}"
                      id="form-jenis"
                      class="form-jenis d-none">
                    @csrf
                    <input type="hidden" name="_method" value="PUT" id="jenis-method" @disabled(! $modeEdit)>
                    <input type="hidden" name="edit_id" id="jenis-edit-id" value="{{ $modeEdit ? old('edit_id') : '' }}">
                    <h2 class="form-jenis-judul" id="judul-form-jenis">{{ $modeEdit ? 'Form Edit Jenis' : 'Form Tambah Jenis' }}</h2>
    
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="jenis-kode" class="form-label">Kode Jenis</label>
                            <input type="text" id="jenis-kode" name="kode" maxlength="10" required
                                   class="form-control text-uppercase {{ $errors->jenis->has('kode') ? 'is-invalid' : '' }}"
                                   value="{{ $errors->jenis->any() ? old('kode') : '' }}">
                            <div class="invalid-feedback">{{ $errors->jenis->first('kode') }}</div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="jenis-nama" class="form-label">Nama Jenis</label>
                            <input type="text" id="jenis-nama" name="nama" maxlength="255" required
                                   class="form-control {{ $errors->jenis->has('nama') ? 'is-invalid' : '' }}"
                                   value="{{ $errors->jenis->any() ? old('nama') : '' }}">
                            <div class="invalid-feedback">{{ $errors->jenis->first('nama') }}</div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="jenis-retensi" class="form-label">Retensi aktif (tahun, kosong = selama berlaku)</label>
                            <input type="number" id="jenis-retensi" name="retensi_tahun" min="1" max="100"
                                   class="form-control {{ $errors->jenis->has('retensi_tahun') ? 'is-invalid' : '' }}"
                                   value="{{ $errors->jenis->any() ? old('retensi_tahun') : '' }}">
                            <div class="invalid-feedback">{{ $errors->jenis->first('retensi_tahun') }}</div>
                        </div>
                    </div>
    
                    <div class="mt-3">
                        <div class="form-label">Dipakai di layanan</div>
                        <div class="row row-cols-2 row-cols-md-3 g-1">
                            @foreach ($daftarLayanan as $layanan)
                                <div class="col">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="layanan_ids[]"
                                               value="{{ $layanan->id }}" id="jenis-layanan-{{ $layanan->id }}"
                                               @checked($errors->jenis->any() && in_array($layanan->id, old('layanan_ids', [])))>
                                        <label class="form-check-label" for="jenis-layanan-{{ $layanan->id }}">{{ $layanan->nama }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if ($errors->jenis->has('layanan_ids'))
                            <div class="pesan-layanan text-danger small mt-1">{{ $errors->jenis->first('layanan_ids') }}</div>
                        @endif
                    </div>
    
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="submit" id="btn-simpan-jenis" class="btn btn-sm btn-form-tambah">{{ $modeEdit ? 'Simpan Perubahan' : '+ Tambah' }}</button>
                        <button type="button" id="btn-kembali-jenis" class="btn btn-sm btn-form-kembali">Kembali</button>
                    </div>
                </form>
            @endif
    
            <div class="table-responsive" id="tabel-jenis-wrap">
                <table class="table table-hover align-middle mb-0 tabel-jenis">
                    <thead>
                        <tr>
                            <th class="text-center">Kode</th>
                            <th class="text-center">Nama Jenis Dokumen</th>
                            <th class="text-center">Retensi</th>
                            <th class="text-center">Layanan</th>
                            @if ($bisaKelola)
                                <th class="text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($daftarJenis as $jenis)
                            <tr>
                                <td class="fw-bold text-center">{{ $jenis->kode }}</td>
                                <td>{{ $jenis->nama }}</td>
                                <td class="text-center">{{ $jenis->retensi_tahun ? $jenis->retensi_tahun.' tahun' : '-' }}</td>
                                <td class="text-center">{{ $jenis->layanan->pluck('nama')->implode(', ') ?: '-' }}</td>
                                @if ($bisaKelola)
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-primary btn-edit-jenis"
                                                    data-action="{{ route('jenis.update', $jenis) }}"
                                                    data-id="{{ $jenis->id }}"
                                                    data-kode="{{ $jenis->kode }}"
                                                    data-nama="{{ $jenis->nama }}"
                                                    data-retensi="{{ $jenis->retensi_tahun }}"
                                                    data-layanan="{{ $jenis->layanan->pluck('id')->toJson() }}">Edit</button>
                                            <form method="POST"
                                                  action="{{ route('jenis.destroy', $jenis) }}"
                                                  data-konfirmasi="Hapus jenis dokumen {{ $jenis->nama }}?">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $bisaKelola ? 5 : 4 }}" class="text-center text-secondary py-4">Belum ada jenis dokumen.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    @if ($bisaKelola)
        <div class="modal fade" id="modal-tambah" tabindex="-1" aria-labelledby="judul-tambah" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('layanan.store') }}" class="modal-content">
                    @csrf
                    <div class="modal-header">
                        <h2 class="modal-title fs-5 text-primary" id="judul-tambah">Tambah Layanan</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="tambah-nama" class="form-label">Nama Layanan</label>
                            <input type="text" id="tambah-nama" name="nama" maxlength="255" required
                                   class="form-control {{ $errors->tambah->has('nama') ? 'is-invalid' : '' }}"
                                   value="{{ $errors->tambah->any() ? old('nama') : '' }}">
                            <div class="invalid-feedback">{{ $errors->tambah->first('nama') }}</div>
                        </div>
                        <div class="mb-3">
                            <label for="tambah-deskripsi" class="form-label">Deskripsi</label>
                            <textarea id="tambah-deskripsi" name="deskripsi" rows="3" maxlength="1000"
                                      class="form-control {{ $errors->tambah->has('deskripsi') ? 'is-invalid' : '' }}">{{ $errors->tambah->any() ? old('deskripsi') : '' }}</textarea>
                            <div class="invalid-feedback">{{ $errors->tambah->first('deskripsi') }}</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    
        <div class="modal fade" id="modal-edit" tabindex="-1" aria-labelledby="judul-edit" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" id="form-edit" action="" class="modal-content">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="edit_id" id="edit-id" value="{{ $errors->edit->any() ? old('edit_id') : '' }}">
                    <div class="modal-header">
                        <h2 class="modal-title fs-5 text-primary" id="judul-edit">Edit Layanan</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit-nama" class="form-label">Nama Layanan</label>
                            <input type="text" id="edit-nama" name="nama" maxlength="255" required
                                   class="form-control {{ $errors->edit->has('nama') ? 'is-invalid' : '' }}"
                                   value="{{ $errors->edit->any() ? old('nama') : '' }}">
                            <div class="invalid-feedback">{{ $errors->edit->first('nama') }}</div>
                        </div>
                        <div class="mb-3">
                            <label for="edit-deskripsi" class="form-label">Deskripsi</label>
                            <textarea id="edit-deskripsi" name="deskripsi" rows="3" maxlength="1000"
                                      class="form-control {{ $errors->edit->has('deskripsi') ? 'is-invalid' : '' }}">{{ $errors->edit->any() ? old('deskripsi') : '' }}</textarea>
                            <div class="invalid-feedback">{{ $errors->edit->first('deskripsi') }}</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    
        @push('scripts')
        <script>
            const modalTambah = document.getElementById('modal-tambah');
            const modalEdit = document.getElementById('modal-edit');
            const formEdit = document.getElementById('form-edit');
            const tombolTambahLayanan = document.getElementById('btn-tambah-layanan');
            const tombolTambahJenis = document.getElementById('btn-tambah-jenis');
            const tombolKembaliJenis = document.getElementById('btn-kembali-jenis');
            const tombolSimpanJenis = document.getElementById('btn-simpan-jenis');
            const panelJenis = document.getElementById('panel-jenis');
            const formJenis = document.getElementById('form-jenis');
            const tabelJenis = document.getElementById('tabel-jenis-wrap');
            const tabJenis = document.querySelector('[data-bs-target="#panel-jenis"]');
            const judulFormJenis = document.getElementById('judul-form-jenis');
            const metodeJenis = document.getElementById('jenis-method');
            const idJenis = document.getElementById('jenis-edit-id');
            let formJenisBuka = false;
    
            function aturTombol() {
                const tabJenisAktif = panelJenis.classList.contains('active');
                tombolTambahLayanan.disabled = tabJenisAktif;
                tombolTambahJenis.disabled = !tabJenisAktif || formJenisBuka;
            }
    
            function kosongkanFormJenis() {
                formJenis.querySelectorAll('input[type="text"], input[type="number"]').forEach(function (el) {
                    el.value = '';
                });
                formJenis.querySelectorAll('input[type="checkbox"]').forEach(function (el) {
                    el.checked = false;
                });
                formJenis.querySelectorAll('.is-invalid').forEach(function (el) {
                    el.classList.remove('is-invalid');
                });
                formJenis.querySelectorAll('.pesan-layanan').forEach(function (el) {
                    el.remove();
                });
            }
    
            function modeTambahJenis() {
                kosongkanFormJenis();
                formJenis.action = formJenis.dataset.urlTambah;
                metodeJenis.disabled = true;
                idJenis.value = '';
                judulFormJenis.textContent = 'Form Tambah Jenis';
                tombolSimpanJenis.textContent = '+ Tambah';
            }
    
            function modeEditJenis(tombol) {
                kosongkanFormJenis();
                const idLayanan = JSON.parse(tombol.dataset.layanan || '[]').map(String);
                formJenis.action = tombol.dataset.action;
                metodeJenis.disabled = false;
                idJenis.value = tombol.dataset.id;
                judulFormJenis.textContent = 'Form Edit Jenis';
                tombolSimpanJenis.textContent = 'Simpan Perubahan';
                document.getElementById('jenis-kode').value = tombol.dataset.kode;
                document.getElementById('jenis-nama').value = tombol.dataset.nama;
                document.getElementById('jenis-retensi').value = tombol.dataset.retensi || '';
                formJenis.querySelectorAll('input[name="layanan_ids[]"]').forEach(function (el) {
                    el.checked = idLayanan.includes(el.value);
                });
            }
    
            function bukaFormJenis() {
                formJenisBuka = true;
                formJenis.classList.remove('d-none');
                tabelJenis.classList.add('d-none');
                aturTombol();
                formJenis.querySelector('input[name="kode"]').focus();
            }
    
            function tutupFormJenis() {
                formJenisBuka = false;
                modeTambahJenis();
                formJenis.classList.add('d-none');
                tabelJenis.classList.remove('d-none');
                aturTombol();
            }
    
            document.querySelectorAll('.tab-layanan [data-bs-toggle="tab"]').forEach(function (tab) {
                tab.addEventListener('shown.bs.tab', aturTombol);
            });
    
            tombolTambahJenis.addEventListener('click', function () {
                modeTambahJenis();
                bukaFormJenis();
            });
    
            tombolKembaliJenis.addEventListener('click', tutupFormJenis);
    
            document.querySelectorAll('.btn-edit-jenis').forEach(function (tombol) {
                tombol.addEventListener('click', function () {
                    modeEditJenis(tombol);
                    bukaFormJenis();
                });
            });
    
            document.querySelectorAll('.modal').forEach(function (modal) {
                modal.addEventListener('shown.bs.modal', function () {
                    const input = modal.querySelector('input[name="nama"]');
                    if (input) {
                        input.focus();
                    }
                });
            });
    
            modalEdit.addEventListener('show.bs.modal', function (event) {
                const tombol = event.relatedTarget;
                if (!tombol) {
                    return;
                }
                formEdit.action = tombol.dataset.action;
                document.getElementById('edit-id').value = tombol.dataset.id;
                document.getElementById('edit-nama').value = tombol.dataset.nama;
                document.getElementById('edit-deskripsi').value = tombol.dataset.deskripsi || '';
                formEdit.querySelectorAll('.is-invalid').forEach(function (el) {
                    el.classList.remove('is-invalid');
                });
            });
    
    
            @if ($errors->tambah->any())
                bootstrap.Modal.getOrCreateInstance(modalTambah).show();
            @endif
    
            @if ($errors->edit->any() && old('edit_id'))
                formEdit.action = {{ Illuminate\Support\Js::from(route('layanan.update', old('edit_id'))) }};
                bootstrap.Modal.getOrCreateInstance(modalEdit).show();
            @endif
    
            @if ($errors->jenis->any())
                bootstrap.Tab.getOrCreateInstance(tabJenis).show();
                bukaFormJenis();
            @elseif (session('tab') === 'jenis')
                bootstrap.Tab.getOrCreateInstance(tabJenis).show();
            @endif
        </script>
        @endpush
    @endif
    
    @push('scripts')
    <script>
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