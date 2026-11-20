<x-superadmin.layout title="Master Portofolio Sampel">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h1 class="h4 fw-bold text-primary mb-1">Master Portofolio Sampel</h1>
            <p class="text-secondary mb-0 small">Kategori komoditas dan sektor pengujian sampel laboratorium nasional maupun unit kerja cabang.</p>
        </div>
        <button type="button" class="btn btn-primary btn-sm rounded-3 px-3 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalTambah">
            + Tambah Portofolio
        </button>
    </div>

    @if (session('sukses'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 small py-2 px-3 shadow-sm border-0 mb-3" role="alert">
            {{ session('sukses') }}
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('galat'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 small py-2 px-3 shadow-sm border-0 mb-3" role="alert">
            {{ session('galat') }}
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 small py-2 px-3 shadow-sm border-0 mb-3" role="alert">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 mb-3" style="background-color: #ffffff;">
        <div class="card-body p-3">
            <form action="{{ route('superadmin.portofolio.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-5 col-lg-4">
                    <input type="text" name="cari" value="{{ $cari }}" class="form-control form-control-sm rounded-3" placeholder="Cari kode atau nama portofolio...">
                </div>
                <div class="col-12 col-md-4 col-lg-3">
                    <select name="cabang_id" class="form-select form-select-sm rounded-3" onchange="this.form.submit()">
                        <option value="nasional" {{ $cabangId === 'nasional' ? 'selected' : '' }}>Standar Nasional (Pusat)</option>
                        @foreach ($daftarCabang as $cb)
                            <option value="{{ $cb->id }}" {{ (string)$cabangId === (string)$cb->id ? 'selected' : '' }}>
                                Khusus {{ $cb->nama }} ({{ $cb->kode }})
                            </option>
                        @endforeach
                        <option value="semua" {{ $cabangId === 'semua' ? 'selected' : '' }}>Semua Cakupan (Gabungan)</option>
                    </select>
                </div>
                <div class="col-auto d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm rounded-3 px-3">Filter</button>
                    @if ($cari || ($cabangId && $cabangId !== 'nasional'))
                        <a href="{{ route('superadmin.portofolio.index') }}" class="btn btn-outline-secondary btn-sm rounded-3 px-3">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background-color: #ffffff;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 tabel-jenis">
                <thead>
                    <tr>
                        <th class="py-2.5 ps-3 align-middle text-center" style="width: 50px;">NO</th>
                        <th class="py-2.5 px-3 align-middle text-center" style="width: 140px;">KODE PORTOFOLIO</th>
                        <th class="py-2.5 px-3 align-middle">NAMA PORTOFOLIO / KOMODITAS</th>
                        <th class="py-2.5 px-3 align-middle text-center" style="width: 170px;">CAKUPAN / CABANG</th>
                        <th class="py-2.5 px-3 align-middle text-center" style="width: 160px;">RETENSI DEFAULT</th>
                        <th class="py-2.5 px-3 align-middle text-center" style="width: 130px;">SAMPEL FISIK</th>
                        <th class="py-2.5 pe-3 align-middle text-center" style="width: 100px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($daftarPortofolio as $item)
                        <tr>
                            <td class="ps-3 text-center text-secondary small">{{ $loop->iteration + ($daftarPortofolio->currentPage() - 1) * $daftarPortofolio->perPage() }}</td>
                            <td class="px-3 text-center">
                                <span class="badge rounded-pill px-2.5 py-1 font-monospace" style="background-color: #e6f4f1; color: #277a72; font-size: 0.82rem;">
                                    {{ $item->kode }}
                                </span>
                            </td>
                            <td class="px-3 fw-semibold text-dark">{{ $item->nama }}</td>
                            <td class="px-3 text-center">
                                @if ($item->cabang)
                                    <span class="badge bg-info-subtle text-info border px-2.5 py-1">
                                        {{ $item->cabang->nama }} ({{ $item->cabang->kode }})
                                    </span>
                                @else
                                    <span class="badge bg-primary-subtle text-primary border px-2.5 py-1">
                                        Standar Nasional
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 text-center">
                                <span class="badge bg-light text-dark border">
                                    {{ $item->retensi_tahun ? $item->retensi_tahun . ' ' . ucfirst($item->retensi_satuan ?? 'bulan') : 'Permanen / Khusus' }}
                                </span>
                            </td>
                            <td class="px-3 text-center">
                                <span class="badge rounded-pill bg-light text-secondary border px-2.5 py-1">
                                    {{ $item->sampel_count }} Sampel
                                </span>
                            </td>
                            <td class="text-center pe-3">
                                <div class="d-flex justify-content-center gap-1">
                                    <button type="button"
                                            class="btn btn-outline-primary btn-sm p-1 rounded-2 d-flex align-items-center justify-content-center"
                                            style="width: 30px; height: 30px;"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEdit"
                                            data-id="{{ $item->id }}"
                                            data-kode="{{ $item->kode }}"
                                            data-nama="{{ $item->nama }}"
                                            data-cabang="{{ $item->cabang_id }}"
                                            data-retensi="{{ $item->retensi_tahun }}"
                                            data-satuan="{{ $item->retensi_satuan ?? 'bulan' }}"
                                            title="Edit">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                    </button>
                                    <form action="{{ route('superadmin.portofolio.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus portofolio {{ $item->nama }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-outline-danger btn-sm p-1 rounded-2 d-flex align-items-center justify-content-center"
                                                style="width: 30px; height: 30px;"
                                                title="Hapus">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted small">Belum ada data portofolio sampel.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($daftarPortofolio->hasPages())
        <div class="mt-3 d-flex justify-content-center">
            {{ $daftarPortofolio->links('pagination::bootstrap-5') }}
        </div>
    @endif

    <div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <form action="{{ route('superadmin.portofolio.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-0 pb-0 pt-3 px-3">
                        <h5 class="modal-title fw-bold text-primary" id="modalTambahLabel">Tambah Portofolio Sampel</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-3">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Cakupan / Asal Cabang</label>
                            <select name="cabang_id" class="form-select">
                                <option value="">Standar Nasional (Semua Cabang)</option>
                                @foreach ($daftarCabang as $cb)
                                    <option value="{{ $cb->id }}">{{ $cb->nama }} ({{ $cb->kode }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Kode Portofolio <span class="text-danger">*</span></label>
                            <input type="text" name="kode" class="form-control text-uppercase" placeholder="Contoh: MNR, MGS, AGR, ENV" maxlength="20" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Nama Portofolio / Komoditas <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" placeholder="Contoh: Mineral dan Batubara" maxlength="255" required>
                        </div>
                        <div>
                            <label class="form-label small fw-semibold text-secondary">Retensi Default Sampel</label>
                            <div class="input-group">
                                <input type="number" name="retensi_tahun" class="form-control" placeholder="Kosongkan jika khusus" min="1" max="100">
                                <select name="retensi_satuan" class="form-select" style="max-width: 120px;">
                                    <option value="bulan" selected>Bulan</option>
                                    <option value="tahun">Tahun</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 pb-3 px-3">
                        <button type="button" class="btn btn-light btn-sm rounded-3 px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm rounded-3 px-3">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <form id="formEditPortofolio" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header border-0 pb-0 pt-3 px-3">
                        <h5 class="modal-title fw-bold text-primary" id="modalEditLabel">Ubah Portofolio Sampel</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-3">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Cakupan / Asal Cabang</label>
                            <select id="edit_cabang_id" name="cabang_id" class="form-select">
                                <option value="">Standar Nasional (Semua Cabang)</option>
                                @foreach ($daftarCabang as $cb)
                                    <option value="{{ $cb->id }}">{{ $cb->nama }} ({{ $cb->kode }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Kode Portofolio <span class="text-danger">*</span></label>
                            <input type="text" id="edit_kode" name="kode" class="form-control text-uppercase" maxlength="20" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Nama Portofolio / Komoditas <span class="text-danger">*</span></label>
                            <input type="text" id="edit_nama" name="nama" class="form-control" maxlength="255" required>
                        </div>
                        <div>
                            <label class="form-label small fw-semibold text-secondary">Retensi Default Sampel</label>
                            <div class="input-group">
                                <input type="number" id="edit_retensi_tahun" name="retensi_tahun" class="form-control" min="1" max="100">
                                <select id="edit_retensi_satuan" name="retensi_satuan" class="form-select" style="max-width: 120px;">
                                    <option value="bulan">Bulan</option>
                                    <option value="tahun">Tahun</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 pb-3 px-3">
                        <button type="button" class="btn btn-light btn-sm rounded-3 px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm rounded-3 px-3">Perbarui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var modalEdit = document.getElementById('modalEdit');
                if (modalEdit) {
                    modalEdit.addEventListener('show.bs.modal', function (event) {
                        var button = event.relatedTarget;
                        var id = button.getAttribute('data-id');
                        var kode = button.getAttribute('data-kode');
                        var nama = button.getAttribute('data-nama');
                        var cabang = button.getAttribute('data-cabang');
                        var retensi = button.getAttribute('data-retensi');
                        var satuan = button.getAttribute('data-satuan');

                        var form = document.getElementById('formEditPortofolio');
                        form.action = '/superadmin/portofolio/' + id;

                        document.getElementById('edit_kode').value = kode;
                        document.getElementById('edit_nama').value = nama;
                        document.getElementById('edit_cabang_id').value = cabang || '';
                        document.getElementById('edit_retensi_tahun').value = retensi || '';
                        document.getElementById('edit_retensi_satuan').value = satuan || 'bulan';
                    });
                }
            });
        </script>
    @endpush
</x-superadmin.layout>