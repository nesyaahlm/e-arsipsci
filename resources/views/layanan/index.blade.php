@extends('layouts.app')

@section('judul', 'Layanan & Jenis')

@section('konten')
@push('css')
<link rel="stylesheet" href="{{ asset('css/layanan.css') }}">
@endpush

<h1 class="h3 fw-bold text-primary mb-1">Layanan &amp; Jenis</h1>
<p class="text-secondary mb-4">Layanan adalah bidang usaha. Jenis dokumen menentukan kode nomor, retensi, dan klasifikasi bawaan.</p>

@if (session('sukses'))
    <div class="alert alert-success">{{ session('sukses') }}</div>
@endif
@if (session('galat'))
    <div class="alert alert-danger">{{ session('galat') }}</div>
@endif

<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 border-bottom border-2 border-primary">
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
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal-tambah">+ Tambah Layanan</button>
            <button type="button" class="btn btn-warning btn-sm" disabled title="Dikerjakan di tahap berikutnya">+ Tambah Jenis</button>
        </div>
    @endif
</div>

<div class="tab-content pt-4">
    <div class="tab-pane fade show active" id="panel-layanan" role="tabpanel">
        <div class="row g-4 justify-content-center">
            @forelse ($daftarLayanan as $layanan)
                <div class="col-12 col-md-6 col-xl-4">
                    <article class="card kartu-layanan h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h3 class="h6 fw-bold text-uppercase text-primary mb-0">{{ $layanan->nama }}</h3>
                                    <p class="small text-secondary mb-0">{{ $layanan->jenisDokumen->count() }} jenis dokumen</p>
                                </div>
                                <span class="kartu-ikon text-primary">@include('layanan._ikon', ['nama' => $layanan->nama])</span>
                            </div>

                            <div class="d-flex flex-column align-items-start gap-1 mt-3">
                                @forelse ($layanan->jenisDokumen as $jenis)
                                    <span class="badge text-bg-light border fw-normal">{{ $jenis->nama }}</span>
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
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 tabel-jenis">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Jenis Dokumen</th>
                            <th>Retensi</th>
                            <th>Layanan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($daftarJenis as $jenis)
                            <tr>
                                <td class="fw-bold">{{ $jenis->kode }}</td>
                                <td>{{ $jenis->nama }}</td>
                                <td>{{ $jenis->retensi_tahun ? $jenis->retensi_tahun.' tahun' : '-' }}</td>
                                <td>{{ $jenis->layanan->pluck('nama')->implode(', ') ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-secondary py-4">Belum ada jenis dokumen.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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
@endif

@if ($bisaKelola)
    @push('skrip')
    <script>
        const modalTambah = document.getElementById('modal-tambah');
        const modalEdit = document.getElementById('modal-edit');
        const formEdit = document.getElementById('form-edit');

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

        document.querySelectorAll('form[data-konfirmasi]').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!confirm(form.dataset.konfirmasi)) {
                    event.preventDefault();
                }
            });
        });

        @if ($errors->tambah->any())
            bootstrap.Modal.getOrCreateInstance(modalTambah).show();
        @endif

        @if ($errors->edit->any() && old('edit_id'))
            formEdit.action = {{ Illuminate\Support\Js::from(route('layanan.update', old('edit_id'))) }};
            bootstrap.Modal.getOrCreateInstance(modalEdit).show();
        @endif
    </script>
    @endpush
@endif
@endsection
