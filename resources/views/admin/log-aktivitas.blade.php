<x-admin.layout title="Log Aktivitas">
    <link rel="stylesheet" href="{{ asset('css/log.css') }}">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 fw-bold text-primary mb-0">Log Aktivitas</h1>
            <p class="text-secondary small mb-0">Rekam jejak aktivitas pengguna. Log lebih dari 7 hari dipindahkan otomatis ke arsip.</p>
        </div>
        <a href="{{ route('log.arsip.index') }}" class="btn btn-sm btn-outline-secondary px-3 shadow-sm">
            Arsip Log
            <span class="badge text-bg-secondary ms-1">{{ $totalArsip }}</span>
        </a>
    </div>

    <form method="GET" action="{{ route('log.index') }}" class="row g-2 align-items-end mb-3">
        <div class="col-12 col-md-6">
            <label for="cari" class="form-label small fw-semibold text-secondary mb-1">Cari</label>
            <div class="input-group input-group-sm log-cari">
                <span class="input-group-text">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/></svg>
                </span>
                <input type="text" class="form-control" id="cari" name="cari" value="{{ $cari }}" placeholder="Cari user atau dokumen....">
            </div>
        </div>

        <div class="col-6 col-md-3">
            <label for="aktivitas" class="form-label small fw-semibold text-secondary mb-1">Aktivitas</label>
            <select class="form-select form-select-sm" id="aktivitas" name="aktivitas" onchange="this.form.submit()">
                <option value="">Semua aktivitas</option>
                @foreach ($daftarAktivitas as $nama)
                    <option value="{{ $nama }}" @selected($aktivitas === $nama)>{{ ucfirst(str_replace('_', ' ', $nama)) }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-6 col-md-3">
            <label for="tanggal" class="form-label small fw-semibold text-secondary mb-1">Tanggal</label>
            <input type="date" class="form-control form-control-sm" id="tanggal" name="tanggal" value="{{ $tanggal }}" onchange="this.form.submit()">
        </div>

        @if ($cari !== '' || $aktivitas || $tanggal)
            <div class="col-12">
                <a href="{{ route('log.index') }}" class="small text-decoration-none">Reset filter</a>
            </div>
        @endif
    </form>

    <div class="log-wrap shadow-sm">
        <div class="table-responsive">
            <table class="table log-tabel align-middle">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>User</th>
                        <th>Aktivitas</th>
                        <th>Dokumen / Rincian</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td class="text-nowrap">{{ $log->created_at->locale('id')->translatedFormat('j M Y H:i') }}</td>
                            <td>{{ $log->user?->name ?? 'Sistem' }}</td>
                            <td>{{ $log->label }}</td>
                            <td>
                                @if ($log->dokumen)
                                    {{ $log->dokumen->nomor }}
                                    @if ($log->dokumen->trashed())
                                        <span class="badge text-bg-light border text-secondary ms-1">dihapus</span>
                                    @endif
                                    @if ($log->keterangan)
                                        <div class="text-secondary" style="font-size: 0.75rem;">{{ $log->keterangan }}</div>
                                    @endif
                                @elseif ($log->keterangan)
                                    {{ $log->keterangan }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-secondary py-5">Belum ada aktivitas yang tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($logs->total() > 0)
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 mt-3">
            <span class="text-secondary small">Menampilkan {{ $logs->firstItem() }} sampai {{ $logs->lastItem() }} dari {{ $logs->total() }} aktivitas</span>
            {{ $logs->links('pagination::bootstrap-5') }}
        </div>
    @endif
</x-admin.layout>