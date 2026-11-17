<x-superadmin.layout title="Log Aktivitas Nasional">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h1 class="h4 fw-bold text-primary mb-1">Log Aktivitas Nasional (Audit Trail)</h1>
            <p class="text-secondary mb-0 small">Rekam jejak, audit kepatuhan, dan kronologi aktivitas pengguna lintas kantor cabang.</p>
        </div>
        <div>
            @if ($tab === 'aktif')
                <button type="button" class="btn btn-primary btn-sm rounded-3 px-3 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalArsipkanLog">
                    + Arsipkan Log Lama
                </button>
            @else
                <form action="{{ route('superadmin.log-aktivitas.hapus-arsip') }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin membersihkan semua arsip log? Tindakan ini permanen.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-3 px-3 py-2 fw-semibold">
                        Bersihkan Semua Arsip Log
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if (session('sukses'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 small py-2 px-3 shadow-sm border-0 mb-3" role="alert">
            {{ session('sukses') }}
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex gap-2 mb-3 flex-wrap">
        <a href="{{ route('superadmin.log-aktivitas.index', array_merge(request()->except('tab', 'page'), ['tab' => 'aktif'])) }}"
           class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold {{ $tab === 'aktif' ? 'btn-primary' : 'btn-light border text-secondary' }}">
            Log Berjalan ({{ $totalAktif }})
        </a>
        <a href="{{ route('superadmin.log-aktivitas.index', array_merge(request()->except('tab', 'page'), ['tab' => 'arsip'])) }}"
           class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold {{ $tab === 'arsip' ? 'btn-primary' : 'btn-light border text-secondary' }}">
            Arsip Log ({{ $totalArsip }})
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-3 bg-white">
        <div class="card-body p-3">
            <form action="{{ route('superadmin.log-aktivitas.index') }}" method="GET" class="row g-2 align-items-center">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <div class="col-12 col-md-3">
                    <input type="text" name="cari" value="{{ $cari }}" class="form-control form-control-sm rounded-3" placeholder="Cari nama user atau rincian...">
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
                    <select name="aktivitas" class="form-select form-select-sm rounded-3">
                        <option value="">Semua Aktivitas</option>
                        @foreach ($daftarAktivitas as $akt)
                            <option value="{{ $akt }}" {{ $aktivitas == $akt ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $akt)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control form-control-sm rounded-3">
                </div>
                <div class="col-auto d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm rounded-3 px-3">Filter</button>
                    @if ($cari || $cabangId || $aktivitas || $tanggal)
                        <a href="{{ route('superadmin.log-aktivitas.index', ['tab' => $tab]) }}" class="btn btn-outline-secondary btn-sm rounded-3 px-3">Reset</a>
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
                        <th class="py-2.5 px-3 align-middle" style="width: 170px;">WAKTU AKTIVITAS</th>
                        <th class="py-2.5 px-3 align-middle" style="width: 200px;">PENGGUNA / PERSONIL</th>
                        <th class="py-2.5 px-3 align-middle" style="width: 150px;">KANTOR CABANG</th>
                        <th class="py-2.5 px-3 align-middle text-center" style="width: 140px;">AKTIVITAS</th>
                        <th class="py-2.5 px-3 align-middle">RINCIAN DOKUMEN / KETERANGAN</th>
                        @if ($tab === 'arsip')
                            <th class="py-2.5 pe-3 align-middle text-center" style="width: 140px;">DIARSIPKAN</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td class="ps-3 text-center text-secondary small">{{ $loop->iteration + ($logs->currentPage() - 1) * $logs->perPage() }}</td>
                            <td class="px-3">
                                <div class="fw-semibold text-dark small">{{ $log->created_at ? $log->created_at->format('d/m/Y H:i') : '-' }}</div>
                                <div class="text-muted" style="font-size: 0.72rem;">{{ $log->created_at ? $log->created_at->diffForHumans() : '' }}</div>
                            </td>
                            <td class="px-3">
                                <div class="fw-semibold text-dark">{{ $log->user->name ?? 'Sistem / Anonim' }}</div>
                                @if ($log->user)
                                    <span class="badge bg-light text-secondary border" style="font-size: 0.7rem;">
                                        Role: {{ ucfirst($log->user->role) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-3">
                                <span class="badge rounded-pill bg-light text-primary border border-primary-subtle px-2.5 py-1">
                                    {{ $log->user->cabang->nama ?? 'Cabang Cilacap' }}
                                </span>
                            </td>
                            <td class="px-3 text-center">
                                @php
                                    $akt = strtolower($log->aktivitas);
                                @endphp
                                @if (str_contains($akt, 'tambah') || str_contains($akt, 'unggah'))
                                    <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-2.5 py-1">
                                        {{ ucfirst(str_replace('_', ' ', $log->aktivitas)) }}
                                    </span>
                                @elseif (str_contains($akt, 'ubah') || str_contains($akt, 'edit'))
                                    <span class="badge rounded-pill bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1">
                                        {{ ucfirst(str_replace('_', ' ', $log->aktivitas)) }}
                                    </span>
                                @elseif (str_contains($akt, 'hapus'))
                                    <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1">
                                        {{ ucfirst(str_replace('_', ' ', $log->aktivitas)) }}
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-info-subtle text-info-emphasis border border-info-subtle px-2.5 py-1">
                                        {{ ucfirst(str_replace('_', ' ', $log->aktivitas)) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 {{ $tab === 'aktif' ? 'pe-3' : '' }}">
                                @if ($log->dokumen)
                                    <span class="badge bg-light text-dark border font-monospace me-1" style="font-size: 0.75rem;">
                                        {{ $log->dokumen->nomor }}
                                    </span>
                                @endif
                                <span class="text-dark small">{{ $log->keterangan ?: '-' }}</span>
                            </td>
                            @if ($tab === 'arsip')
                                <td class="px-3 pe-3 text-center text-secondary small">
                                    {{ $log->diarsipkan_pada ? $log->diarsipkan_pada->format('d/m/Y') : '-' }}
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $tab === 'arsip' ? 7 : 6 }}" class="text-center py-4 text-muted small">
                                Tidak ada rekam jejak aktivitas pada tab ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($logs->hasPages())
            <div class="card-footer bg-white border-0 py-3 px-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                <div class="small text-muted">
                    Menampilkan {{ $logs->firstItem() }}–{{ $logs->lastItem() }} dari {{ $logs->total() }} log aktivitas
                </div>
                <div>
                    {{ $logs->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>

    <div class="modal fade" id="modalArsipkanLog" tabindex="-1" aria-labelledby="modalArsipkanLogLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <form action="{{ route('superadmin.log-aktivitas.arsipkan') }}" method="POST">
                    @csrf
                    <div class="modal-header border-0 pb-0 pt-3 px-3">
                        <h5 class="modal-title fw-bold text-primary" id="modalArsipkanLogLabel">Arsipkan Log Aktivitas</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-3">
                        <p class="text-secondary small mb-3">
                            Pindahkan riwayat log aktivitas yang sudah lampau ke tab <strong>Arsip Log</strong> untuk menjaga tabel utama tetap ringan dan cepat.
                        </p>
                        <div>
                            <label class="form-label small fw-semibold text-secondary">Pilih Kriteria Umur Log</label>
                            <select name="hari" class="form-select rounded-3">
                                <option value="7">Lebih dari 7 hari yang lalu</option>
                                <option value="30" selected>Lebih dari 30 hari yang lalu (1 Bulan)</option>
                                <option value="90">Lebih dari 90 hari yang lalu (3 Bulan)</option>
                                <option value="365">Lebih dari 1 tahun yang lalu</option>
                                <option value="730">Lebih dari 2 tahun yang lalu</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 pb-3 px-3">
                        <button type="button" class="btn btn-light btn-sm rounded-3 px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm rounded-3 px-3">Proses Pengarsipan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-superadmin.layout>