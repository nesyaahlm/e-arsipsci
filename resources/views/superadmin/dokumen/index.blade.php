<x-superadmin.layout title="Dokumen Nasional">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h1 class="h4 fw-bold text-primary mb-1">Dokumen Nasional</h1>
            <p class="text-secondary mb-0 small">Monitoring, audit siklus hidup arsip, dan pengawasan hapus buku lintas kantor unit kerja.</p>
        </div>
    </div>

    @if (session('galat'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 small py-2 px-3 shadow-sm border-0 mb-3" role="alert">
            {{ session('galat') }}
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex gap-2 mb-3 flex-wrap">
        <a href="{{ route('superadmin.dokumen.index', array_merge(request()->except('status_arsip', 'page'), ['status_arsip' => 'semua'])) }}"
           class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold {{ $statusArsip === 'semua' ? 'btn-primary' : 'btn-light border' }}">
            Semua ({{ $totalSemua }})
        </a>
        <a href="{{ route('superadmin.dokumen.index', array_merge(request()->except('status_arsip', 'page'), ['status_arsip' => 'aktif'])) }}"
           class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold {{ $statusArsip === 'aktif' ? 'btn-success text-white' : 'btn-light border text-success' }}">
            Aktif ({{ $totalAktif }})
        </a>
        <a href="{{ route('superadmin.dokumen.index', array_merge(request()->except('status_arsip', 'page'), ['status_arsip' => 'inaktif'])) }}"
           class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold {{ $statusArsip === 'inaktif' ? 'btn-warning text-dark' : 'btn-light border text-warning' }}" style="{{ $statusArsip === 'inaktif' ? 'background-color: #ffc107;' : '' }}">
            Inaktif ({{ $totalInaktif }})
        </a>
        <a href="{{ route('superadmin.dokumen.index', array_merge(request()->except('status_arsip', 'page'), ['status_arsip' => 'hapus_buku'])) }}"
           class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold {{ $statusArsip === 'hapus_buku' ? 'btn-danger text-white' : 'btn-light border text-danger' }}">
            Hapus Buku ({{ $totalHapusBuku }})
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-3 bg-white">
        <div class="card-body p-3">
            <form action="{{ route('superadmin.dokumen.index') }}" method="GET" class="row g-2 align-items-center">
                <input type="hidden" name="status_arsip" value="{{ $statusArsip }}">
                <div class="col-12 col-md-3">
                    <input type="text" name="cari" value="{{ $cari }}" class="form-control form-control-sm rounded-3" placeholder="Cari nomor, judul, atau no buku...">
                </div>
                <div class="col-12 col-md-3">
                    <select name="cabang_id" class="form-select form-select-sm rounded-3">
                        <option value="">Semua Kantor Unit Kerja</option>
                        @foreach ($daftarCabang as $c)
                            <option value="{{ $c->id }}" {{ $cabangId == $c->id ? 'selected' : '' }}>{{ $c->nama }} ({{ $c->kode }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <select name="bagian_id" class="form-select form-select-sm rounded-3">
                        <option value="">Semua Bagian</option>
                        @foreach ($daftarBagian as $b)
                            <option value="{{ $b->id }}" {{ $bagianId == $b->id ? 'selected' : '' }}>{{ $b->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <select name="tahun" class="form-select form-select-sm rounded-3">
                        <option value="">Semua Tahun</option>
                        @foreach ($daftarTahun as $t)
                            <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm rounded-3 px-3">Filter</button>
                    @if ($cari || $cabangId || $bagianId || $tahun || $statusArsip !== 'semua')
                        <a href="{{ route('superadmin.dokumen.index') }}" class="btn btn-outline-secondary btn-sm rounded-3 px-3">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 tabel-jenis">
                <thead>
                    <tr>
                        <th class="py-2.5 ps-3 align-middle text-center" style="width: 50px;">NO</th>
                        <th class="py-2.5 px-3 align-middle" style="width: 160px;">NOMOR</th>
                        <th class="py-2.5 px-3 align-middle">JUDUL DOKUMEN</th>
                        <th class="py-2.5 px-3 align-middle" style="width: 150px;">UNIT KERJA</th>
                        <th class="py-2.5 px-3 align-middle" style="width: 150px;">BAGIAN</th>
                        <th class="py-2.5 px-3 align-middle text-center" style="width: 110px;">TGL DOKUMEN</th>
                        <th class="py-2.5 px-3 align-middle text-center" style="width: 110px;">STATUS SIKLUS</th>
                        <th class="py-2.5 px-3 align-middle" style="width: 160px;">FISIK / NO BUKU</th>
                        <th class="py-2.5 pe-3 align-middle text-center" style="width: 90px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($daftarDokumen as $item)
                        <tr>
                            <td class="ps-3 text-center text-secondary small">{{ $loop->iteration + ($daftarDokumen->currentPage() - 1) * $daftarDokumen->perPage() }}</td>
                            <td class="px-3">
                                <span class="badge rounded-pill px-2.5 py-1 font-monospace" style="background-color: #e6f4f1; color: #277a72; font-size: 0.8rem;">
                                    {{ $item->nomor ?: '-' }}
                                </span>
                            </td>
                            <td class="px-3">
                                <div class="fw-semibold text-dark">{{ $item->judul }}</div>
                                @if ($item->unit)
                                    <span class="badge bg-light text-muted border" style="font-size: 0.72rem;">Unit: {{ $item->unit }}</span>
                                @endif
                            </td>
                            <td class="px-3">
                                <span class="badge rounded-pill bg-light text-primary border border-primary-subtle px-2.5 py-1">
                                    {{ $item->cabang->nama ?? 'Cabang Cilacap' }}
                                </span>
                            </td>
                            <td class="px-3">
                                <span class="badge bg-light text-dark border">{{ $item->bagian->nama ?? '-' }}</span>
                            </td>
                            <td class="px-3 text-center text-secondary small">
                                {{ $item->tanggal_dokumen ? $item->tanggal_dokumen->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-3 text-center">
                                @if ($item->status_arsip === 'aktif')
                                    <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-2.5 py-1">Aktif</span>
                                @elseif ($item->status_arsip === 'inaktif')
                                    <span class="badge rounded-pill bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1">Inaktif</span>
                                @else
                                    <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1">Hapus Buku</span>
                                @endif
                            </td>
                            <td class="px-3">
                                @if ($item->status_arsip === 'hapus_buku')
                                    <span class="badge bg-light text-danger border font-monospace" style="font-size: 0.75rem;">BA: {{ $item->nomor_buku ?: '-' }}</span>
                                    @if ($item->tanggal_hapus_buku)
                                        <div class="text-muted" style="font-size: 0.72rem;">{{ $item->tanggal_hapus_buku->format('d/m/Y') }}</div>
                                    @endif
                                @else
                                    <span class="small text-secondary">{{ $item->lokasi_arsip_fisik ?: 'Belum ditata fisik' }}</span>
                                @endif
                            </td>
                            <td class="text-center pe-3">
                                <div class="d-flex justify-content-center gap-1">
                                    <button type="button"
                                            class="btn btn-outline-primary btn-sm p-1 rounded-2 d-flex align-items-center justify-content-center"
                                            style="width: 30px; height: 30px;"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalDetailDokumen"
                                            data-nomor="{{ $item->nomor }}"
                                            data-judul="{{ $item->judul }}"
                                            data-cabang="{{ $item->cabang->nama ?? 'Cabang Cilacap' }}"
                                            data-bagian="{{ $item->bagian->nama ?? '-' }}"
                                            data-unit="{{ $item->unit ?: '-' }}"
                                            data-tanggal="{{ $item->tanggal_dokumen ? $item->tanggal_dokumen->format('d F Y') : '-' }}"
                                            data-retensi="{{ $item->retensi_sampai ? $item->retensi_sampai->format('d F Y') : '-' }}"
                                            data-status="{{ $item->status_arsip }}"
                                            data-lokasi="{{ $item->lokasi_arsip_fisik ?: '-' }}"
                                            data-nomor-buku="{{ $item->nomor_buku ?: '-' }}"
                                            data-tanggal-hapus="{{ $item->tanggal_hapus_buku ? $item->tanggal_hapus_buku->format('d F Y') : '-' }}"
                                            data-keterangan-hapus="{{ $item->keterangan_hapus_buku ?: '-' }}"
                                            data-pengunggah="{{ $item->pengunggah->name ?? '-' }}"
                                            data-deskripsi="{{ $item->deskripsi ?: '-' }}"
                                            data-unduh="{{ route('superadmin.dokumen.unduh', $item->id) }}"
                                            data-preview="{{ route('superadmin.dokumen.preview', $item->id) }}"
                                            data-has-file="{{ $item->file_path ? '1' : '0' }}"
                                            title="Lihat Detail">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>
                                    @if ($item->file_path)
                                        <a href="{{ route('superadmin.dokumen.unduh', $item->id) }}"
                                           class="btn btn-outline-secondary btn-sm p-1 rounded-2 d-flex align-items-center justify-content-center"
                                           style="width: 30px; height: 30px;"
                                           title="Unduh Berkas">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted small">Tidak ada data dokumen ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    
    @if ($daftarDokumen->hasPages())
        <div class="card-footer bg-white border-0 py-3 px-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <div class="small text-muted">
                Menampilkan {{ $daftarDokumen->firstItem() }}–{{ $daftarDokumen->lastItem() }} dari {{ $daftarDokumen->total() }} dokumen
            </div>
            <div>
                {{ $daftarDokumen->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif

    <div class="modal fade" id="modalDetailDokumen" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-0 pb-0 pt-3 px-4">
                    <div>
                        <span class="badge rounded-pill mb-1" id="modal-status-badge"></span>
                        <h5 class="modal-title fw-bold text-primary mb-0" id="modalDetailLabel">Detail Dokumen Nasional</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label small text-secondary fw-semibold mb-1">Nomor Dokumen / Surat</label>
                            <div class="fw-bold text-dark font-monospace" id="modal-nomor">-</div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small text-secondary fw-semibold mb-1">Kantor Unit Kerja</label>
                            <div class="fw-bold text-primary" id="modal-cabang">-</div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small text-secondary fw-semibold mb-1">Bagian / Divisi</label>
                            <div class="fw-semibold text-dark" id="modal-bagian">-</div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small text-secondary fw-semibold mb-1">Unit Pengunggah</label>
                            <div class="text-dark" id="modal-unit">-</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small text-secondary fw-semibold mb-1">Judul Dokumen</label>
                            <div class="fw-bold text-dark fs-6" id="modal-judul">-</div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small text-secondary fw-semibold mb-1">Diunggah Oleh</label>
                            <div class="text-dark" id="modal-pengunggah">-</div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small text-secondary fw-semibold mb-1">Tanggal Dokumen</label>
                            <div class="text-dark" id="modal-tanggal">-</div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small text-secondary fw-semibold mb-1">Masa Retensi Sampai</label>
                            <div class="text-dark" id="modal-retensi">-</div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small text-secondary fw-semibold mb-1">Lokasi Arsip Fisik</label>
                            <div class="text-dark bg-light p-2 rounded-3 small" id="modal-lokasi">-</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small text-secondary fw-semibold mb-1">Deskripsi / Ringkasan</label>
                            <div class="text-dark small" id="modal-deskripsi">-</div>
                        </div>

                        <div class="col-12 d-none" id="card-hapus-buku">
                            <div class="card border-danger-subtle bg-danger-subtle rounded-3 p-3">
                                <h6 class="fw-bold text-danger mb-2">Informasi Berita Acara Hapus Buku</h6>
                                <div class="row g-2 small">
                                    <div class="col-12 col-md-6">
                                        <span class="text-secondary">Nomor Berita Acara / Buku:</span>
                                        <div class="fw-bold font-monospace text-dark" id="modal-nomor-buku">-</div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <span class="text-secondary">Tanggal Hapus Buku:</span>
                                        <div class="fw-bold text-dark" id="modal-tanggal-hapus">-</div>
                                    </div>
                                    <div class="col-12">
                                        <span class="text-secondary">Keterangan / Alasan Penghapusan:</span>
                                        <div class="text-dark" id="modal-keterangan-hapus">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4 justify-content-between">
                    <button type="button" class="btn btn-light btn-sm rounded-3 px-3" data-bs-dismiss="modal">Tutup</button>
                    <div class="d-flex gap-2">
                        <a href="#" id="btn-preview-modal" target="_blank" class="btn btn-outline-primary btn-sm rounded-3 px-3">Lihat Berkas</a>
                        <a href="#" id="btn-unduh-modal" class="btn btn-primary btn-sm rounded-3 px-3">Unduh Berkas</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var modalDetail = document.getElementById('modalDetailDokumen');
                if (modalDetail) {
                    modalDetail.addEventListener('show.bs.modal', function (event) {
                        var button = event.relatedTarget;
                        var nomor = button.getAttribute('data-nomor') || '-';
                        var judul = button.getAttribute('data-judul') || '-';
                        var cabang = button.getAttribute('data-cabang') || '-';
                        var bagian = button.getAttribute('data-bagian') || '-';
                        var unit = button.getAttribute('data-unit') || '-';
                        var tanggal = button.getAttribute('data-tanggal') || '-';
                        var retensi = button.getAttribute('data-retensi') || '-';
                        var status = button.getAttribute('data-status') || 'aktif';
                        var lokasi = button.getAttribute('data-lokasi') || '-';
                        var nomorBuku = button.getAttribute('data-nomor-buku') || '-';
                        var tanggalHapus = button.getAttribute('data-tanggal-hapus') || '-';
                        var ketHapus = button.getAttribute('data-keterangan-hapus') || '-';
                        var pengunggah = button.getAttribute('data-pengunggah') || '-';
                        var deskripsi = button.getAttribute('data-deskripsi') || '-';
                        var unduhUrl = button.getAttribute('data-unduh');
                        var previewUrl = button.getAttribute('data-preview');
                        var hasFile = button.getAttribute('data-has-file') === '1';

                        document.getElementById('modal-nomor').textContent = nomor;
                        document.getElementById('modal-judul').textContent = judul;
                        document.getElementById('modal-cabang').textContent = cabang;
                        document.getElementById('modal-bagian').textContent = bagian;
                        document.getElementById('modal-unit').textContent = unit;
                        document.getElementById('modal-tanggal').textContent = tanggal;
                        document.getElementById('modal-retensi').textContent = retensi;
                        document.getElementById('modal-lokasi').textContent = lokasi;
                        document.getElementById('modal-pengunggah').textContent = pengunggah;
                        document.getElementById('modal-deskripsi').textContent = deskripsi;

                        var badge = document.getElementById('modal-status-badge');
                        var cardHapusBuku = document.getElementById('card-hapus-buku');

                        if (status === 'hapus_buku') {
                            badge.className = 'badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-3 py-1';
                            badge.textContent = 'Status: Hapus Buku';
                            cardHapusBuku.classList.remove('d-none');
                            document.getElementById('modal-nomor-buku').textContent = nomorBuku;
                            document.getElementById('modal-tanggal-hapus').textContent = tanggalHapus;
                            document.getElementById('modal-keterangan-hapus').textContent = ketHapus;
                        } else if (status === 'inaktif') {
                            badge.className = 'badge rounded-pill bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1';
                            badge.textContent = 'Status: Inaktif';
                            cardHapusBuku.classList.add('d-none');
                        } else {
                            badge.className = 'badge rounded-pill bg-success-subtle text-success border border-success-subtle px-3 py-1';
                            badge.textContent = 'Status: Aktif';
                            cardHapusBuku.classList.add('d-none');
                        }

                        var btnPreview = document.getElementById('btn-preview-modal');
                        var btnUnduh = document.getElementById('btn-unduh-modal');
                        if (hasFile) {
                            btnPreview.classList.remove('d-none');
                            btnPreview.href = previewUrl;
                            btnUnduh.classList.remove('d-none');
                            btnUnduh.href = unduhUrl;
                        } else {
                            btnPreview.classList.add('d-none');
                            btnUnduh.classList.add('d-none');
                        }
                    });
                }
            });
        </script>
    @endpush
</x-superadmin.layout>