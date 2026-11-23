<x-superadmin.layout title="Master Klasifikasi Arsip">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h1 class="h4 fw-bold text-primary mb-1">Master Klasifikasi Arsip</h1>
            <p class="text-secondary mb-0 small">Standarisasi kode klasifikasi dan masa retensi arsip untuk dokumen dan sampel komoditas.</p>
        </div>
        <button type="button" class="btn btn-primary btn-sm rounded-3 px-3 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalTambah">
            + Tambah Klasifikasi
        </button>
    </div>

    @if (session('sukses'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 small py-2 px-3 shadow-sm border-0 mb-3" role="alert">
            {{ session('sukses') }}
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

    <ul class="nav nav-pills mb-3 p-1 rounded-3 bg-white shadow-sm d-inline-flex border" role="tablist">
        <li class="nav-item">
            <a class="nav-link py-2 px-3 fw-semibold small {{ $tab === 'dokumen' ? 'active bg-primary text-white' : 'text-secondary' }}"
               href="{{ route('superadmin.kode-surat.index', ['tab' => 'dokumen']) }}">
                Klasifikasi Dokumen (Bagian)
                <span class="badge {{ $tab === 'dokumen' ? 'bg-white text-primary' : 'bg-secondary' }} ms-1">{{ $jumlahDokumen }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link py-2 px-3 fw-semibold small {{ $tab === 'sampel' ? 'active bg-primary text-white' : 'text-secondary' }}"
               href="{{ route('superadmin.kode-surat.index', ['tab' => 'sampel']) }}">
                Klasifikasi Sampel (Portofolio)
                <span class="badge {{ $tab === 'sampel' ? 'bg-white text-primary' : 'bg-secondary' }} ms-1">{{ $jumlahSampel }}</span>
            </a>
        </li>
    </ul>

    <div class="card border-0 shadow-sm rounded-4 mb-3" style="background-color: #ffffff;">
        <div class="card-body p-3">
            <form action="{{ route('superadmin.kode-surat.index') }}" method="GET" class="row g-2 align-items-center">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <div class="col-12 col-md-5 col-lg-4">
                    <input type="text" name="cari" value="{{ $cari }}" class="form-control form-control-sm rounded-3" placeholder="Cari kode, nama, atau keterangan...">
                </div>

                @if ($tab === 'dokumen')
                    <div class="col-12 col-md-4 col-lg-3">
                        <select name="bagian_id" class="form-select form-select-sm rounded-3">
                            <option value="">Semua Bagian</option>
                            @foreach ($daftarBagian as $b)
                                <option value="{{ $b->id }}" {{ $bagianId == $b->id ? 'selected' : '' }}>{{ $b->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <div class="col-12 col-md-4 col-lg-3">
                        <select name="portofolio_id" class="form-select form-select-sm rounded-3">
                            <option value="">Semua Portofolio</option>
                            @foreach ($daftarPortofolio as $p)
                                <option value="{{ $p->id }}" {{ $portofolioId == $p->id ? 'selected' : '' }}>{{ $p->nama }} ({{ $p->kode }})</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="col-auto d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm rounded-3 px-3">Filter</button>
                    @if ($cari || ($tab === 'dokumen' && $bagianId) || ($tab === 'sampel' && $portofolioId))
                        <a href="{{ route('superadmin.kode-surat.index', ['tab' => $tab]) }}" class="btn btn-outline-secondary btn-sm rounded-3 px-3">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background-color: #ffffff;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="--bs-table-bg: #e3f1ee; --bs-table-color: #1f5f55; --bs-table-border-color: #a3cfbb; font-size: 0.82rem;">
                    <tr>
                        <th class="py-2.5 ps-3 align-middle fw-bold text-center" style="width: 50px;">NO</th>
                        <th class="py-2.5 px-3 align-middle fw-bold" style="width: 130px;">KODE</th>
                        <th class="py-2.5 px-3 align-middle fw-bold" style="width: 180px;">{{ $tab === 'dokumen' ? 'BAGIAN' : 'PORTOFOLIO' }}</th>
                        <th class="py-2.5 px-3 align-middle fw-bold">NAMA KLASIFIKASI</th>
                        <th class="py-2.5 px-3 align-middle fw-bold text-center" style="width: 120px;">RETENSI AKTIF</th>
                        <th class="py-2.5 px-3 align-middle fw-bold text-center" style="width: 120px;">RETENSI INAKTIF</th>
                        <th class="py-2.5 px-3 align-middle fw-bold">KETERANGAN</th>
                        <th class="py-2.5 pe-3 align-middle fw-bold text-center" style="width: 110px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($daftarKodeSurat as $item)
                        <tr>
                            <td class="ps-3 text-center text-secondary small">{{ $loop->iteration + ($daftarKodeSurat->currentPage() - 1) * $daftarKodeSurat->perPage() }}</td>
                            <td class="px-3">
                                <span class="badge rounded-pill px-2.5 py-1 font-monospace" style="background-color: #e6f4f1; color: #277a72; font-size: 0.82rem;">
                                    {{ $item->kode }}
                                </span>
                            </td>
                            <td class="px-3">
                                @if ($tab === 'dokumen')
                                    <span class="badge bg-light text-dark border">{{ $item->bagian->nama ?? '-' }}</span>
                                @else
                                    <span class="badge bg-light text-dark border">{{ $item->portofolio->nama ?? '-' }}</span>
                                @endif
                            </td>
                            <td class="px-3 fw-semibold text-dark">{{ $item->nama }}</td>
                            <td class="px-3 text-center"><span class="badge bg-light text-dark border">{{ $item->retensi_aktif }} Thn</span></td>
                            <td class="px-3 text-center"><span class="badge bg-light text-secondary border">{{ $item->retensi_inaktif }} Thn</span></td>
                            <td class="px-3 text-muted small">{{ $item->keterangan ?: '-' }}</td>
                            <td class="text-center pe-3">
                                <div class="d-flex justify-content-center gap-1">
                                    <button type="button"
                                            class="btn btn-outline-primary btn-sm p-1 rounded-2 d-flex align-items-center justify-content-center"
                                            style="width: 30px; height: 30px;"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEdit"
                                            data-id="{{ $item->id }}"
                                            data-bagian-id="{{ $item->bagian_id }}"
                                            data-portofolio-id="{{ $item->portofolio_id }}"
                                            data-kode="{{ $item->kode }}"
                                            data-nama="{{ $item->nama }}"
                                            data-aktif="{{ $item->retensi_aktif }}"
                                            data-inaktif="{{ $item->retensi_inaktif }}"
                                            data-keterangan="{{ $item->keterangan }}"
                                            title="Edit">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                    </button>
                                    <form action="{{ route('superadmin.kode-surat.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus klasifikasi {{ $item->kode }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="tab" value="{{ $tab }}">
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
                            <td colspan="8" class="text-center py-4 text-muted small">Belum ada data klasifikasi {{ $tab === 'dokumen' ? 'dokumen' : 'sampel' }}.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($daftarKodeSurat->hasPages())
        <div class="mt-3 d-flex justify-content-center">
            {{ $daftarKodeSurat->links('pagination::bootstrap-5') }}
        </div>
    @endif

    <div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <form action="{{ route('superadmin.kode-surat.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    <div class="modal-header border-0 pb-0 pt-3 px-3">
                        <h5 class="modal-title fw-bold text-primary" id="modalTambahLabel">Tambah Klasifikasi {{ $tab === 'dokumen' ? 'Dokumen' : 'Sampel' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-3">
                        @if ($tab === 'dokumen')
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Bagian / Divisi <span class="text-danger">*</span></label>
                                <select name="bagian_id" class="form-select" required>
                                    <option value="" disabled selected>Pilih Bagian</option>
                                    @foreach ($daftarBagian as $b)
                                        <option value="{{ $b->id }}">{{ $b->nama }} ({{ $b->kode }})</option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Portofolio Komoditas <span class="text-danger">*</span></label>
                                <select name="portofolio_id" class="form-select" required>
                                    <option value="" disabled selected>Pilih Portofolio</option>
                                    @foreach ($daftarPortofolio as $p)
                                        <option value="{{ $p->id }}">{{ $p->nama }} ({{ $p->kode }})</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Kode Klasifikasi <span class="text-danger">*</span></label>
                            <input type="text" name="kode" class="form-control text-uppercase" placeholder="{{ $tab === 'dokumen' ? 'Contoh: KWI, INV, LHU' : 'Contoh: MNR, MGS, ENV' }}" maxlength="20" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Nama Klasifikasi <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" placeholder="{{ $tab === 'dokumen' ? 'Contoh: Kuitansi Pembayaran' : 'Contoh: Batubara / Nikel' }}" maxlength="255" required>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Retensi Aktif (Tahun) <span class="text-danger">*</span></label>
                                <input type="number" name="retensi_aktif" class="form-control" value="2" min="1" max="50" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Retensi Inaktif (Tahun) <span class="text-danger">*</span></label>
                                <input type="number" name="retensi_inaktif" class="form-control" value="5" min="0" max="50" required>
                            </div>
                        </div>
                        <div>
                            <label class="form-label small fw-semibold text-secondary">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan aturan kearsipan..."></textarea>
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
                <form id="formEditKodeSurat" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    <div class="modal-header border-0 pb-0 pt-3 px-3">
                        <h5 class="modal-title fw-bold text-primary" id="modalEditLabel">Ubah Klasifikasi {{ $tab === 'dokumen' ? 'Dokumen' : 'Sampel' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-3">
                        @if ($tab === 'dokumen')
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Bagian / Divisi <span class="text-danger">*</span></label>
                                <select id="edit_bagian_id" name="bagian_id" class="form-select" required>
                                    <option value="" disabled>Pilih Bagian</option>
                                    @foreach ($daftarBagian as $b)
                                        <option value="{{ $b->id }}">{{ $b->nama }} ({{ $b->kode }})</option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Portofolio Komoditas <span class="text-danger">*</span></label>
                                <select id="edit_portofolio_id" name="portofolio_id" class="form-select" required>
                                    <option value="" disabled>Pilih Portofolio</option>
                                    @foreach ($daftarPortofolio as $p)
                                        <option value="{{ $p->id }}">{{ $p->nama }} ({{ $p->kode }})</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Kode Klasifikasi <span class="text-danger">*</span></label>
                            <input type="text" id="edit_kode" name="kode" class="form-control text-uppercase" maxlength="20" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Nama Klasifikasi <span class="text-danger">*</span></label>
                            <input type="text" id="edit_nama" name="nama" class="form-control" maxlength="255" required>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Retensi Aktif (Tahun) <span class="text-danger">*</span></label>
                                <input type="number" id="edit_aktif" name="retensi_aktif" class="form-control" min="1" max="50" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Retensi Inaktif (Tahun) <span class="text-danger">*</span></label>
                                <input type="number" id="edit_inaktif" name="retensi_inaktif" class="form-control" min="0" max="50" required>
                            </div>
                        </div>
                        <div>
                            <label class="form-label small fw-semibold text-secondary">Keterangan</label>
                            <textarea id="edit_keterangan" name="keterangan" class="form-control" rows="2"></textarea>
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
                        var bagianId = button.getAttribute('data-bagian-id');
                        var portofolioId = button.getAttribute('data-portofolio-id');
                        var kode = button.getAttribute('data-kode');
                        var nama = button.getAttribute('data-nama');
                        var aktif = button.getAttribute('data-aktif');
                        var inaktif = button.getAttribute('data-inaktif');
                        var keterangan = button.getAttribute('data-keterangan');

                        var form = document.getElementById('formEditKodeSurat');
                        form.action = '/superadmin/kode-surat/' + id;

                        var selectBagian = document.getElementById('edit_bagian_id');
                        if (selectBagian && bagianId) {
                            selectBagian.value = bagianId;
                        }

                        var selectPortofolio = document.getElementById('edit_portofolio_id');
                        if (selectPortofolio && portofolioId) {
                            selectPortofolio.value = portofolioId;
                        }

                        document.getElementById('edit_kode').value = kode;
                        document.getElementById('edit_nama').value = nama;
                        document.getElementById('edit_aktif').value = aktif;
                        document.getElementById('edit_inaktif').value = inaktif;
                        document.getElementById('edit_keterangan').value = keterangan || '';
                    });
                }
            });
        </script>
    @endpush
</x-superadmin.layout>