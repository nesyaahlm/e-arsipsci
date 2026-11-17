<x-superadmin.layout title="Sampel Nasional">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h1 class="h4 fw-bold text-primary mb-1">Sampel Nasional</h1>
            <p class="text-secondary mb-0 small">Monitoring, audit arsip sampel fisik laboratorium, dan pengawasan hapus buku lintas kantor cabang.</p>
        </div>
    </div>

    @if (session('galat'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 small py-2 px-3 shadow-sm border-0 mb-3" role="alert">
            {{ session('galat') }}
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex gap-2 mb-3 flex-wrap">
        <a href="{{ route('superadmin.sampel.index', array_merge(request()->except('status_arsip', 'page'), ['status_arsip' => 'semua'])) }}"
           class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold {{ $statusArsip === 'semua' ? 'btn-primary' : 'btn-light border' }}">
            Semua ({{ $totalSemua }})
        </a>
        <a href="{{ route('superadmin.sampel.index', array_merge(request()->except('status_arsip', 'page'), ['status_arsip' => 'aktif'])) }}"
           class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold {{ $statusArsip === 'aktif' ? 'btn-success text-white' : 'btn-light border text-success' }}">
            Aktif ({{ $totalAktif }})
        </a>
        <a href="{{ route('superadmin.sampel.index', array_merge(request()->except('status_arsip', 'page'), ['status_arsip' => 'inaktif'])) }}"
           class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold {{ $statusArsip === 'inaktif' ? 'btn-warning text-dark' : 'btn-light border text-warning' }}" style="{{ $statusArsip === 'inaktif' ? 'background-color: #ffc107;' : '' }}">
            Inaktif ({{ $totalInaktif }})
        </a>
        <a href="{{ route('superadmin.sampel.index', array_merge(request()->except('status_arsip', 'page'), ['status_arsip' => 'hapus_buku'])) }}"
           class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold {{ $statusArsip === 'hapus_buku' ? 'btn-danger text-white' : 'btn-light border text-danger' }}">
            Hapus Buku ({{ $totalHapusBuku }})
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-3 bg-white">
        <div class="card-body p-3">
            <form action="{{ route('superadmin.sampel.index') }}" method="GET" class="row g-2 align-items-center">
                <input type="hidden" name="status_arsip" value="{{ $statusArsip }}">
                <div class="col-12 col-md-3">
                    <input type="text" name="cari" value="{{ $cari }}" class="form-control form-control-sm rounded-3" placeholder="Cari kode, nama, shipper, vessel, ruang...">
                </div>
                <div class="col-12 col-md-3">
                    <select name="cabang_id" class="form-select form-select-sm rounded-3">
                        <option value="">Semua Kantor Cabang</option>
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
                        <a href="{{ route('superadmin.sampel.index') }}" class="btn btn-outline-secondary btn-sm rounded-3 px-3">Reset</a>
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
                        <th class="py-2.5 px-3 align-middle" style="width: 150px;">KODE SAMPEL</th>
                        <th class="py-2.5 px-3 align-middle">NAMA SAMPEL / KOMODITAS</th>
                        <th class="py-2.5 px-3 align-middle" style="width: 150px;">CABANG</th>
                        <th class="py-2.5 px-3 align-middle" style="width: 140px;">BAGIAN</th>
                        <th class="py-2.5 px-3 align-middle text-center" style="width: 110px;">TGL MASUK</th>
                        <th class="py-2.5 px-3 align-middle text-center" style="width: 110px;">STATUS SIKLUS</th>
                        <th class="py-2.5 px-3 align-middle" style="width: 170px;">RUANG / NO BUKU</th>
                        <th class="py-2.5 pe-3 align-middle text-center" style="width: 90px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($daftarSampel as $item)
                        <tr>
                            <td class="ps-3 text-center text-secondary small">{{ $loop->iteration + ($daftarSampel->currentPage() - 1) * $daftarSampel->perPage() }}</td>
                            <td class="px-3">
                                <span class="badge rounded-pill px-2.5 py-1 font-monospace" style="background-color: #e6f4f1; color: #277a72; font-size: 0.8rem;">
                                    {{ $item->kode_sampel }}
                                </span>
                            </td>
                            <td class="px-3">
                                <div class="fw-semibold text-dark">{{ $item->nama_sampel }}</div>
                                @if ($item->shipper || $item->vessel)
                                    <div class="small text-muted" style="font-size: 0.73rem;">
                                        {{ $item->shipper ?: '-' }} {{ $item->vessel ? '• '.$item->vessel : '' }}
                                    </div>
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
                                {{ $item->tanggal_masuk_arsip ? $item->tanggal_masuk_arsip->format('d/m/Y') : '-' }}
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
                                    <span class="badge bg-light text-dark border font-monospace" style="font-size: 0.75rem;">
                                        {{ $item->nomor_ruang_arsip }} / {{ $item->nama_ruang_arsip }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-center pe-3">
                                <div class="d-flex justify-content-center gap-1">
                                    <button type="button"
                                            class="btn btn-outline-primary btn-sm p-1 rounded-2 d-flex align-items-center justify-content-center"
                                            style="width: 30px; height: 30px;"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalDetailSampel"
                                            data-kode="{{ $item->kode_sampel }}"
                                            data-nama="{{ $item->nama_sampel }}"
                                            data-cabang="{{ $item->cabang->nama ?? 'Cabang Cilacap' }}"
                                            data-bagian="{{ $item->bagian->nama ?? '-' }}"
                                            data-shipper="{{ $item->shipper ?: '-' }}"
                                            data-vessel="{{ $item->vessel ?: '-' }}"
                                            data-bl="{{ $item->bill_of_lading ?: '-' }}"
                                            data-tgl-ambil="{{ $item->tanggal_pengambilan ? $item->tanggal_pengambilan->format('d F Y') : '-' }}"
                                            data-tempat-ambil="{{ $item->tempat_pengambilan ?: '-' }}"
                                            data-analysis="{{ $item->analysis_for ?: '-' }}"
                                            data-ruang="{{ $item->nomor_ruang_arsip }} - {{ $item->nama_ruang_arsip }}"
                                            data-tgl-masuk="{{ $item->tanggal_masuk_arsip ? $item->tanggal_masuk_arsip->format('d F Y') : '-' }}"
                                            data-pengunggah="{{ $item->pengunggah->name ?? '-' }}"
                                            data-deskripsi="{{ $item->deskripsi ?: '-' }}"
                                            data-status="{{ $item->status_arsip }}"
                                            data-nomor-buku="{{ $item->nomor_buku ?: '-' }}"
                                            data-tanggal-hapus="{{ $item->tanggal_hapus_buku ? $item->tanggal_hapus_buku->format('d F Y') : '-' }}"
                                            data-keterangan-hapus="{{ $item->keterangan_hapus_buku ?: '-' }}"
                                            data-foto-url="{{ $item->foto_path ? route('superadmin.sampel.foto', $item->id) : '' }}"
                                            title="Lihat Detail">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted small">Tidak ada data sampel ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($daftarSampel->hasPages())
            <div class="card-footer bg-white border-0 py-3 px-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                <div class="small text-muted">
                    Menampilkan {{ $daftarSampel->firstItem() }}–{{ $daftarSampel->lastItem() }} dari {{ $daftarSampel->total() }} sampel
                </div>
                <div>
                    {{ $daftarSampel->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>

    <div class="modal fade" id="modalDetailSampel" tabindex="-1" aria-labelledby="modalDetailSampelLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-0 pb-0 pt-3 px-4">
                    <div>
                        <span class="badge rounded-pill mb-1" id="modal-status-badge"></span>
                        <h5 class="modal-title fw-bold text-primary mb-0" id="modalDetailSampelLabel">Detail Sampel Nasional</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label small text-secondary fw-semibold mb-1">Kode Sampel</label>
                            <div class="fw-bold text-dark font-monospace" id="modal-kode">-</div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small text-secondary fw-semibold mb-1">Kantor Cabang</label>
                            <div class="fw-bold text-primary" id="modal-cabang">-</div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small text-secondary fw-semibold mb-1">Nama Sampel / Komoditas</label>
                            <div class="fw-bold text-dark fs-6" id="modal-nama">-</div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small text-secondary fw-semibold mb-1">Bagian / Divisi</label>
                            <div class="fw-semibold text-dark" id="modal-bagian">-</div>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small text-secondary fw-semibold mb-1">Shipper</label>
                            <div class="text-dark" id="modal-shipper">-</div>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small text-secondary fw-semibold mb-1">Vessel (Kapal)</label>
                            <div class="text-dark" id="modal-vessel">-</div>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small text-secondary fw-semibold mb-1">Bill of Lading (BL)</label>
                            <div class="text-dark" id="modal-bl">-</div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small text-secondary fw-semibold mb-1">Tanggal Pengambilan</label>
                            <div class="text-dark" id="modal-tgl-ambil">-</div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small text-secondary fw-semibold mb-1">Tempat Pengambilan</label>
                            <div class="text-dark" id="modal-tempat-ambil">-</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small text-secondary fw-semibold mb-1">Analysis For (Tujuan Analisis)</label>
                            <div class="text-dark bg-light p-2 rounded-3 small" id="modal-analysis">-</div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small text-secondary fw-semibold mb-1">Ruang / Rak Penyimpanan Arsip</label>
                            <div class="text-dark fw-semibold" id="modal-ruang">-</div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small text-secondary fw-semibold mb-1">Tanggal Masuk Arsip</label>
                            <div class="text-dark" id="modal-tgl-masuk">-</div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small text-secondary fw-semibold mb-1">Diunggah Oleh</label>
                            <div class="text-dark" id="modal-pengunggah">-</div>
                        </div>
                        <div class="col-12 col-md-6" id="wrap-foto-sampel">
                            <label class="form-label small text-secondary fw-semibold mb-1">Foto Sampel</label>
                            <div>
                                <a href="#" id="link-foto-sampel" target="_blank" class="btn btn-outline-primary btn-sm rounded-3 px-3">
                                    Buka Foto Fisik
                                </a>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small text-secondary fw-semibold mb-1">Deskripsi Tambahan</label>
                            <div class="text-dark small" id="modal-deskripsi">-</div>
                        </div>

                        <div class="col-12 d-none" id="card-hapus-buku-sampel">
                            <div class="card border-danger-subtle bg-danger-subtle rounded-3 p-3">
                                <h6 class="fw-bold text-danger mb-2">Informasi Berita Acara Hapus Buku</h6>
                                <div class="row g-2 small">
                                    <div class="col-12 col-md-6">
                                        <span class="text-secondary">Nomor Berita Acara / Buku:</span>
                                        <div class="fw-bold font-monospace text-dark" id="modal-nomor-buku-sampel">-</div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <span class="text-secondary">Tanggal Hapus Buku:</span>
                                        <div class="fw-bold text-dark" id="modal-tanggal-hapus-sampel">-</div>
                                    </div>
                                    <div class="col-12">
                                        <span class="text-secondary">Keterangan / Alasan Pemusnahan:</span>
                                        <div class="text-dark" id="modal-keterangan-hapus-sampel">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light btn-sm rounded-3 px-3" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var modalDetail = document.getElementById('modalDetailSampel');
                if (modalDetail) {
                    modalDetail.addEventListener('show.bs.modal', function (event) {
                        var button = event.relatedTarget;
                        var kode = button.getAttribute('data-kode') || '-';
                        var nama = button.getAttribute('data-nama') || '-';
                        var cabang = button.getAttribute('data-cabang') || '-';
                        var bagian = button.getAttribute('data-bagian') || '-';
                        var shipper = button.getAttribute('data-shipper') || '-';
                        var vessel = button.getAttribute('data-vessel') || '-';
                        var bl = button.getAttribute('data-bl') || '-';
                        var tglAmbil = button.getAttribute('data-tgl-ambil') || '-';
                        var tempatAmbil = button.getAttribute('data-tempat-ambil') || '-';
                        var analysis = button.getAttribute('data-analysis') || '-';
                        var ruang = button.getAttribute('data-ruang') || '-';
                        var tglMasuk = button.getAttribute('data-tgl-masuk') || '-';
                        var pengunggah = button.getAttribute('data-pengunggah') || '-';
                        var deskripsi = button.getAttribute('data-deskripsi') || '-';
                        var status = button.getAttribute('data-status') || 'aktif';
                        var nomorBuku = button.getAttribute('data-nomor-buku') || '-';
                        var tanggalHapus = button.getAttribute('data-tanggal-hapus') || '-';
                        var ketHapus = button.getAttribute('data-keterangan-hapus') || '-';
                        var fotoUrl = button.getAttribute('data-foto-url');

                        document.getElementById('modal-kode').textContent = kode;
                        document.getElementById('modal-nama').textContent = nama;
                        document.getElementById('modal-cabang').textContent = cabang;
                        document.getElementById('modal-bagian').textContent = bagian;
                        document.getElementById('modal-shipper').textContent = shipper;
                        document.getElementById('modal-vessel').textContent = vessel;
                        document.getElementById('modal-bl').textContent = bl;
                        document.getElementById('modal-tgl-ambil').textContent = tglAmbil;
                        document.getElementById('modal-tempat-ambil').textContent = tempatAmbil;
                        document.getElementById('modal-analysis').textContent = analysis;
                        document.getElementById('modal-ruang').textContent = ruang;
                        document.getElementById('modal-tgl-masuk').textContent = tglMasuk;
                        document.getElementById('modal-pengunggah').textContent = pengunggah;
                        document.getElementById('modal-deskripsi').textContent = deskripsi;

                        var badge = document.getElementById('modal-status-badge');
                        var cardHapus = document.getElementById('card-hapus-buku-sampel');

                        if (status === 'hapus_buku') {
                            badge.className = 'badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-3 py-1';
                            badge.textContent = 'Status: Hapus Buku';
                            cardHapus.classList.remove('d-none');
                            document.getElementById('modal-nomor-buku-sampel').textContent = nomorBuku;
                            document.getElementById('modal-tanggal-hapus-sampel').textContent = tanggalHapus;
                            document.getElementById('modal-keterangan-hapus-sampel').textContent = ketHapus;
                        } else if (status === 'inaktif') {
                            badge.className = 'badge rounded-pill bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1';
                            badge.textContent = 'Status: Inaktif';
                            cardHapus.classList.add('d-none');
                        } else {
                            badge.className = 'badge rounded-pill bg-success-subtle text-success border border-success-subtle px-3 py-1';
                            badge.textContent = 'Status: Aktif';
                            cardHapus.classList.add('d-none');
                        }

                        var wrapFoto = document.getElementById('wrap-foto-sampel');
                        var linkFoto = document.getElementById('link-foto-sampel');
                        if (fotoUrl) {
                            wrapFoto.classList.remove('d-none');
                            linkFoto.href = fotoUrl;
                        } else {
                            wrapFoto.classList.add('d-none');
                        }
                    });
                }
            });
        </script>
    @endpush
</x-superadmin.layout>