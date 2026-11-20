@php
    $adalahAdmin = Auth::user()->role === 'admin';
    $jumlahKolom = $adalahAdmin ? 7 : 5;
    $namaTipe = $tipe === 'dokumen' ? 'Dokumen' : 'Sampel';
@endphp

<div class="card shadow-sm rounded-4" style="border: 1.5px solid #cfdedb;">
    <div class="card-header bg-white border-0 pt-3 pb-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h2 class="h6 fw-bold mb-0" style="color: #1f5f55;">Riwayat Pemusnahan Fisik {{ $namaTipe }}</h2>
        @if ($adalahAdmin)
            <div class="d-flex gap-2">
                <button type="button" id="btn-hapus-riwayat-terpilih-{{ $tipe }}" class="btn btn-outline-danger btn-sm rounded-3 px-3" disabled>
                    Hapus Terpilih (<span id="jumlah-riwayat-terpilih-{{ $tipe }}">0</span>)
                </button>
                <button type="button" id="btn-hapus-riwayat-semua-{{ $tipe }}" class="btn btn-danger btn-sm rounded-3 px-3" @disabled($riwayat->isEmpty())>
                    Hapus Semua Riwayat
                </button>
            </div>
        @endif
    </div>

    @if ($adalahAdmin)
        <form id="form-hapus-riwayat-{{ $tipe }}" method="POST" action="{{ route('pemusnahan.destroy-riwayat-massal') }}">
            @csrf
            @method('DELETE')
            <input type="hidden" name="tipe_riwayat" value="{{ $tipe }}">
            <input type="hidden" name="mode_riwayat" id="mode-riwayat-{{ $tipe }}" value="terpilih">
        </form>
    @endif

    <div class="table-responsive">
        <table class="table table-sm small align-middle mb-0">
            <thead style="--bs-table-bg: #e3f1ee; --bs-table-color: #1f5f55; font-size: 0.8rem;">
                <tr>
                    @if ($adalahAdmin)
                        <th class="text-center py-2" style="width: 44px;">
                            <input type="checkbox" id="cek-semua-riwayat-{{ $tipe }}" class="form-check-input" @disabled($riwayat->isEmpty())>
                        </th>
                    @endif
                    <th class="text-center fw-bold py-2">Nomor Pemusnahan</th>
                    <th class="text-center fw-bold py-2">Tanggal</th>
                    <th class="text-center fw-bold py-2">Pelaksana</th>
                    <th class="text-center fw-bold py-2">Jumlah</th>
                    <th class="text-center fw-bold py-2">Bukti</th>
                    @if ($adalahAdmin)
                        <th class="text-center fw-bold py-2">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse ($riwayat as $p)
                    @php
                        $itemTampil = $p->items->take(3);
                        $itemSisa = $p->items->slice(3);
                    @endphp
                    <tr>
                        @if ($adalahAdmin)
                            <td class="text-center">
                                <input type="checkbox" class="form-check-input cek-riwayat-{{ $tipe }}" name="ids_riwayat[]" value="{{ $p->id }}" form="form-hapus-riwayat-{{ $tipe }}">
                            </td>
                        @endif
                        <td class="text-center font-monospace text-nowrap fw-semibold">{{ $p->nomor }}</td>
                        <td class="text-center text-nowrap">{{ $p->tanggal->format('d/m/Y') }}</td>
                        <td class="text-center">{{ $p->pelaksana }}</td>
                        <td class="text-center">{{ $p->items_count }}</td>
                        <td class="text-center">
                            <a href="{{ Storage::disk('public')->url($p->bukti_path) }}" target="_blank" rel="noopener" class="link-dark link-underline-opacity-25">Lihat</a>
                        </td>
                        @if ($adalahAdmin)
                            <td class="text-center">
                                <form method="POST" action="{{ route('pemusnahan.destroy-riwayat', $p->id) }}" data-konfirmasi="Hapus riwayat pemusnahan {{ $tipe }} nomor {{ $p->nomor }} ini?">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="tipe" value="{{ $tipe }}">
                                    <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2">Hapus</button>
                                </form>
                            </td>
                        @endif
                    </tr>
                    <tr>
                        <td colspan="{{ $jumlahKolom }}" class="bg-light px-4 py-2">
                            <ul class="mb-0 ps-3">
                                @foreach ($itemTampil as $it)
                                    <li>
                                        <span class="badge bg-secondary-subtle text-secondary me-1">{{ ucfirst($it->tipe) }}</span>
                                        <span class="font-monospace">{{ $it->kode }}</span> &ndash; {{ $it->judul }}
                                        <span class="text-muted">(lokasi semula: {{ $it->lokasi_sebelumnya ?: '-' }})</span>
                                    </li>
                                @endforeach
                            </ul>
                            @if ($itemSisa->isNotEmpty())
                                <div class="collapse" id="rincian-{{ $tipe }}-{{ $p->id }}">
                                    <ul class="mb-0 ps-3 mt-1">
                                        @foreach ($itemSisa as $it)
                                            <li>
                                                <span class="badge bg-secondary-subtle text-secondary me-1">{{ ucfirst($it->tipe) }}</span>
                                                <span class="font-monospace">{{ $it->kode }}</span> &ndash; {{ $it->judul }}
                                                <span class="text-muted">(lokasi semula: {{ $it->lokasi_sebelumnya ?: '-' }})</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <button type="button" class="btn btn-link btn-sm p-0 mt-1 text-decoration-none" data-bs-toggle="collapse" data-bs-target="#rincian-{{ $tipe }}-{{ $p->id }}" aria-expanded="false" aria-controls="rincian-{{ $tipe }}-{{ $p->id }}">
                                    Tampilkan / sembunyikan {{ $itemSisa->count() }} lainnya
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $jumlahKolom }}" class="text-center text-muted py-4">Belum ada riwayat pemusnahan {{ $tipe }}.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($riwayat->hasPages())
        <div class="card-footer bg-white border-0 py-3 px-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <div class="small text-muted">
                Menampilkan {{ $riwayat->firstItem() }}&ndash;{{ $riwayat->lastItem() }} dari {{ $riwayat->total() }} riwayat pemusnahan {{ $tipe }}
            </div>
            <div>
                <nav aria-label="Navigasi Halaman Riwayat {{ $namaTipe }}">
                    <ul class="pagination pagination-sm mb-0">
                        @if ($riwayat->onFirstPage())
                            <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $riwayat->previousPageUrl() }}" rel="prev">&laquo;</a></li>
                        @endif

                        @foreach ($riwayat->getUrlRange(1, $riwayat->lastPage()) as $page => $url)
                            @if ($page == 1 || $page == $riwayat->lastPage() || abs($page - $riwayat->currentPage()) <= 1)
                                @if ($page == $riwayat->currentPage())
                                    <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                                @else
                                    <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                                @endif
                            @elseif ($page == $riwayat->currentPage() - 2 || $page == $riwayat->currentPage() + 2)
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            @endif
                        @endforeach

                        @if ($riwayat->hasMorePages())
                            <li class="page-item"><a class="page-link" href="{{ $riwayat->nextPageUrl() }}" rel="next">&raquo;</a></li>
                        @else
                            <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
                        @endif
                    </ul>
                </nav>
            </div>
        </div>
    @endif
</div>