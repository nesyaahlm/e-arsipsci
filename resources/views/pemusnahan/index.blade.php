<x-dynamic-component :component="$layout" title="Pemusnahan">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/konfirmasi.css') }}">
    @endpush

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h1 class="h3 fw-bold text-primary mb-1">Pemusnahan</h1>
            <p class="text-muted mb-0">Kelola antrean pemusnahan dokumen/sampel dan lihat riwayat arsip yang telah dimusnahkan.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" id="btn-musnah-terpilih" class="btn btn-danger btn-sm rounded-3 px-3" @disabled($antrean->isEmpty()) disabled>
                Musnahkan Terpilih (<span id="jumlah-terpilih">0</span>)
            </button>
            <button type="button" id="btn-musnah-semua" class="btn btn-outline-danger btn-sm rounded-3 px-3" @disabled($antrean->isEmpty())>
                Musnahkan Semua
            </button>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show alert-otomatis small" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show alert-otomatis small" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif
    @if ($errors->has('ids') || $errors->has('tipe'))
        <div class="alert alert-danger alert-dismissible fade show small" role="alert">
            {{ $errors->first('ids') ?: $errors->first('tipe') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    <ul class="nav nav-tabs mb-3" id="pemusnahanTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="antrean-tab" data-bs-toggle="tab" data-bs-target="#antrean-pane" type="button" role="tab" aria-controls="antrean-pane" aria-selected="true">
                Menunggu Dimusnahkan <span class="badge bg-danger ms-1">{{ $antrean->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="riwayat-tab" data-bs-toggle="tab" data-bs-target="#riwayat-pane" type="button" role="tab" aria-controls="riwayat-pane" aria-selected="false">
                Riwayat Pemusnahan <span class="badge bg-secondary ms-1">{{ $riwayat->total() }}</span>
            </button>
        </li>
    </ul>

    <div class="tab-content" id="pemusnahanTabContent">
        <div class="tab-pane fade show active" id="antrean-pane" role="tabpanel" aria-labelledby="antrean-tab" tabindex="0">
            <div class="card shadow-sm rounded-4 mb-4" style="border: 1.5px solid #cfdedb;">
                <div class="card-header bg-white border-0 pt-3 pb-0 px-4 d-flex justify-content-between align-items-center">
                    <h2 class="h6 fw-bold mb-0" style="color: #1f5f55;">Daftar Menunggu Dimusnahkan ({{ $antrean->count() }})</h2>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-sm small align-middle mb-0">
                        <thead style="--bs-table-bg: #e3f1ee; --bs-table-color: #1f5f55; font-size: 0.8rem;">
                            <tr>
                                <th class="text-center py-2" style="width: 44px;">
                                    <input type="checkbox" id="cek-semua-antrean" class="form-check-input" @disabled($antrean->isEmpty())>
                                </th>
                                <th class="text-center fw-bold py-2">Jenis</th>
                                <th class="text-center fw-bold py-2">Kode / Nomor</th>
                                <th class="text-center fw-bold py-2">Judul / Nama</th>
                                <th class="text-center fw-bold py-2">Lokasi Fisik</th>
                                <th class="text-center fw-bold py-2">Diajukan Oleh</th>
                                <th class="text-center fw-bold py-2">Tanggal Diajukan</th>
                                <th class="text-center fw-bold py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($antrean as $item)
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" class="form-check-input cek-antrean" name="ids[]" value="{{ $item->id }}" form="form-musnah" @checked(in_array($item->id, old('ids', [])))>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $item->tipe === 'dokumen' ? 'bg-primary-subtle text-primary' : 'bg-warning-subtle text-warning-emphasis' }}">
                                            {{ ucfirst($item->tipe) }}
                                        </span>
                                    </td>
                                    <td class="text-center font-monospace text-nowrap">{{ $item->kode }}</td>
                                    <td>{{ $item->judul }}</td>
                                    <td class="text-center text-nowrap">{{ $item->lokasi_sebelumnya ?: '-' }}</td>
                                    <td class="text-center">{{ $item->pengaju->name ?? '-' }}</td>
                                    <td class="text-center text-nowrap">{{ $item->created_at->format('d/m/Y') }}</td>
                                    <td class="text-center">
                                        <form method="POST" action="{{ route('pemusnahan.batalkan', $item) }}" data-konfirmasi="Keluarkan {{ $item->kode }} dari daftar pemusnahan? Statusnya akan dikembalikan.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-secondary py-0 px-2">Keluarkan</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        Belum ada data antrean. Pilih dokumen atau sampel lalu klik tombol Pemusnahan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="riwayat-pane" role="tabpanel" aria-labelledby="riwayat-tab" tabindex="0">
            <div class="card shadow-sm rounded-4" style="border: 1.5px solid #cfdedb;">
                <div class="card-header bg-white border-0 pt-3 pb-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h2 class="h6 fw-bold mb-0" style="color: #1f5f55;">Riwayat Pemusnahan Fisik Arsip</h2>
                    @if (Auth::user()->role === 'admin')
                        <div class="d-flex gap-2">
                            <button type="button" id="btn-hapus-riwayat-terpilih" class="btn btn-outline-danger btn-sm rounded-3 px-3" disabled>
                                Hapus Terpilih (<span id="jumlah-riwayat-terpilih">0</span>)
                            </button>
                            <button type="button" id="btn-hapus-riwayat-semua" class="btn btn-danger btn-sm rounded-3 px-3" @disabled($riwayat->isEmpty())>
                                Hapus Semua Riwayat
                            </button>
                        </div>
                    @endif
                </div>

                @if (Auth::user()->role === 'admin')
                    <form id="form-hapus-riwayat-massal" method="POST" action="{{ route('pemusnahan.destroy-riwayat-massal') }}">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="mode_riwayat" id="mode-riwayat" value="terpilih">
                    </form>
                @endif

                <div class="table-responsive">
                    <table class="table table-sm small align-middle mb-0">
                        <thead style="--bs-table-bg: #e3f1ee; --bs-table-color: #1f5f55; font-size: 0.8rem;">
                            <tr>
                                @if (Auth::user()->role === 'admin')
                                    <th class="text-center py-2" style="width: 44px;">
                                        <input type="checkbox" id="cek-semua-riwayat" class="form-check-input" @disabled($riwayat->isEmpty())>
                                    </th>
                                @endif
                                <th class="text-center fw-bold py-2">Nomor Pemusnahan</th>
                                <th class="text-center fw-bold py-2">Tanggal</th>
                                <th class="text-center fw-bold py-2">Pelaksana</th>
                                <th class="text-center fw-bold py-2">Jumlah</th>
                                <th class="text-center fw-bold py-2">Bukti</th>
                                <th class="text-center fw-bold py-2">Rincian</th>
                                @if (Auth::user()->role === 'admin')
                                    <th class="text-center fw-bold py-2">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($riwayat as $p)
                                <tr>
                                    @if (Auth::user()->role === 'admin')
                                        <td class="text-center">
                                            <input type="checkbox" class="form-check-input cek-riwayat" name="ids_riwayat[]" value="{{ $p->id }}" form="form-hapus-riwayat-massal">
                                        </td>
                                    @endif
                                    <td class="text-center font-monospace text-nowrap fw-semibold">{{ $p->nomor }}</td>
                                    <td class="text-center text-nowrap">{{ $p->tanggal->format('d/m/Y') }}</td>
                                    <td class="text-center">{{ $p->pelaksana }}</td>
                                    <td class="text-center">{{ $p->items_count }}</td>
                                    <td class="text-center">
                                        <a href="{{ Storage::disk('public')->url($p->bukti_path) }}" target="_blank" rel="noopener" class="link-dark link-underline-opacity-25">Lihat</a>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" data-bs-toggle="collapse" data-bs-target="#rincian-{{ $p->id }}">Lihat</button>
                                    </td>
                                    @if (Auth::user()->role === 'admin')
                                        <td class="text-center">
                                            <form method="POST" action="{{ route('pemusnahan.destroy-riwayat', $p->id) }}" data-konfirmasi="Hapus riwayat pemusnahan nomor {{ $p->nomor }} ini?">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2">Hapus</button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                                <tr class="collapse" id="rincian-{{ $p->id }}">
                                    <td colspan="{{ Auth::user()->role === 'admin' ? 8 : 6 }}" class="bg-light px-4 py-2">
                                        <ul class="mb-0 ps-3">
                                            @foreach ($p->items as $it)
                                                <li>
                                                    <span class="badge bg-secondary-subtle text-secondary me-1">{{ ucfirst($it->tipe) }}</span>
                                                    <span class="font-monospace">{{ $it->kode }}</span> &ndash; {{ $it->judul }}
                                                    <span class="text-muted">(lokasi semula: {{ $it->lokasi_sebelumnya ?: '-' }})</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ Auth::user()->role === 'admin' ? 8 : 6 }}" class="text-center text-muted py-4">Belum ada riwayat pemusnahan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($riwayat->hasPages())
                    <div class="card-footer bg-white border-0 py-3 px-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                        <div class="small text-muted">
                            Menampilkan {{ $riwayat->firstItem() }}&ndash;{{ $riwayat->lastItem() }} dari {{ $riwayat->total() }} riwayat pemusnahan
                        </div>
                        <div>
                            <nav aria-label="Navigasi Halaman Riwayat">
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
        </div>
    </div>

    <div class="modal fade" id="modalMusnah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-danger">Form Pemusnahan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <form id="form-musnah" method="POST" action="{{ route('pemusnahan.musnahkan') }}" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <input type="hidden" name="mode" id="musnah-mode" value="{{ old('mode', 'terpilih') }}">

                    <div class="modal-body p-4">
                        <div class="alert alert-warning small py-2 mb-3">
                            <span id="ringkasan-musnah" class="fw-semibold"></span>. Lokasi fisiknya akan diubah menjadi <strong>-</strong>, sedangkan data dan berkas digitalnya tetap tersimpan di sistem dengan status Pemusnahan.
                        </div>

                        @if ($errors->musnah->any())
                            <div class="alert alert-danger small py-2 mb-3">
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->musnah->all() as $pesan)
                                        <li>{{ $pesan }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="musnah-nomor" class="form-label small fw-semibold text-secondary">Nomor Pemusnahan <span class="text-danger">*</span></label>
                            <input type="text" id="musnah-nomor" name="nomor" value="{{ old('nomor') }}" maxlength="100" required class="form-control rounded-3 {{ $errors->musnah->has('nomor') ? 'is-invalid' : '' }}" placeholder="Contoh: BAP/CLC/2026/0001">
                        </div>

                        <div class="mb-3">
                            <label for="musnah-tanggal" class="form-label small fw-semibold text-secondary">Tanggal Pemusnahan <span class="text-danger">*</span></label>
                            <input type="date" id="musnah-tanggal" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required class="form-control rounded-3 {{ $errors->musnah->has('tanggal') ? 'is-invalid' : '' }}">
                        </div>

                        <div class="mb-3">
                            <label for="musnah-pelaksana" class="form-label small fw-semibold text-secondary">Pelaksana Pemusnahan <span class="text-danger">*</span></label>
                            <input type="text" id="musnah-pelaksana" name="pelaksana" value="{{ old('pelaksana') }}" maxlength="255" required class="form-control rounded-3 {{ $errors->musnah->has('pelaksana') ? 'is-invalid' : '' }}" placeholder="Nama orang yang melakukan pemusnahan">
                        </div>

                        <div class="mb-0">
                            <label for="musnah-bukti" class="form-label small fw-semibold text-secondary">Bukti Pemusnahan <span class="text-danger">*</span></label>
                            <input type="file" id="musnah-bukti" name="bukti" accept=".pdf,.jpg,.jpeg,.png" required class="form-control rounded-3 {{ $errors->musnah->has('bukti') ? 'is-invalid' : '' }}">
                            <small class="text-muted" style="font-size: 0.72rem;">Format: PDF, JPG, PNG (maks. 10 MB)</small>
                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-0 pb-4 px-4">
                        <button type="button" class="btn btn-light rounded-3 px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" id="btn-submit-musnah" class="btn btn-danger rounded-3 px-4">Musnahkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="kf-overlay" id="kf-overlay" hidden>
        <div class="kf-dialog" role="alertdialog" aria-modal="true" aria-labelledby="kf-judul" aria-describedby="kf-pesan">
            <div class="kf-ikon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M10.3 3.9L1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/>
                    <path d="M12 9v4M12 17h.01"/>
                </svg>
            </div>
            <h2 class="kf-judul" id="kf-judul">Apakah Anda Yakin?</h2>
            <p class="kf-pesan" id="kf-pesan"></p>
            <p class="kf-catatan">Data yang dihapus tidak dapat dikembalikan.</p>
            <div class="kf-aksi">
                <button type="button" class="kf-batal" id="kf-batal">Batal</button>
                <button type="button" class="kf-ya" id="kf-ya">Ya, Keluarka</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const elModal = document.getElementById('modalMusnah');
        const modalMusnah = new bootstrap.Modal(elModal);
        const kotak = Array.from(document.querySelectorAll('.cek-antrean'));
        const cekSemua = document.getElementById('cek-semua-antrean');
        const btnTerpilih = document.getElementById('btn-musnah-terpilih');
        const btnSemua = document.getElementById('btn-musnah-semua');
        const jumlahTerpilih = document.getElementById('jumlah-terpilih');
        const inputMode = document.getElementById('musnah-mode');
        const ringkasan = document.getElementById('ringkasan-musnah');
        const formMusnah = document.getElementById('form-musnah');
        const btnSubmit = document.getElementById('btn-submit-musnah');

        const tabAntrean = document.getElementById('antrean-tab');
        const tabRiwayat = document.getElementById('riwayat-tab');
        const paneAntrean = document.getElementById('antrean-pane');
        const paneRiwayat = document.getElementById('riwayat-pane');
        const badgeAntrean = tabAntrean.querySelector('.badge');
        const badgeRiwayat = tabRiwayat.querySelector('.badge');

        function aturWarnaTab(aktifTab) {
            if (aktifTab === 'riwayat') {
                tabRiwayat.classList.add('active', 'fw-bold');
                tabRiwayat.style.color = '#1f5f55';
                tabRiwayat.classList.remove('text-secondary');

                badgeRiwayat.className = 'badge bg-success ms-1';
                badgeAntrean.className = 'badge bg-secondary ms-1';

                tabAntrean.classList.remove('active', 'fw-bold');
                tabAntrean.classList.add('text-secondary');
                tabAntrean.style.color = '';

                paneRiwayat.classList.add('show', 'active');
                paneAntrean.classList.remove('show', 'active');
            } else {
                tabAntrean.classList.add('active', 'fw-bold');
                tabAntrean.style.color = '#1f5f55';
                tabAntrean.classList.remove('text-secondary');

                badgeAntrean.className = 'badge bg-danger ms-1';
                badgeRiwayat.className = 'badge bg-secondary ms-1';

                tabRiwayat.classList.remove('active', 'fw-bold');
                tabRiwayat.classList.add('text-secondary');
                tabRiwayat.style.color = '';

                paneAntrean.classList.add('show', 'active');
                paneRiwayat.classList.remove('show', 'active');
            }
        }

        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('riwayat') || localStorage.getItem('active_tab') === 'riwayat') {
            aturWarnaTab('riwayat');
        } else {
            aturWarnaTab('antrean');
        }

        tabAntrean.addEventListener('click', function () {
            aturWarnaTab('antrean');
            localStorage.setItem('active_tab', 'antrean');
        });

        tabRiwayat.addEventListener('click', function () {
            aturWarnaTab('riwayat');
            localStorage.setItem('active_tab', 'riwayat');
        });

        function jumlahDipilih() {
            return kotak.filter(function (k) { return k.checked; }).length;
        }

        function segarkan() {
            const n = jumlahDipilih();
            jumlahTerpilih.textContent = n;
            btnTerpilih.disabled = n === 0;
            if (cekSemua) {
                cekSemua.checked = n > 0 && n === kotak.length;
                cekSemua.indeterminate = n > 0 && n < kotak.length;
            }
        }

        function isiRingkasan() {
            const n = inputMode.value === 'semua' ? kotak.length : jumlahDipilih();
            ringkasan.textContent = n + ' data akan dimusnahkan';
        }

        function bukaModal(mode) {
            inputMode.value = mode;
            isiRingkasan();
            modalMusnah.show();
        }

        kotak.forEach(function (k) {
            k.addEventListener('change', segarkan);
        });

        if (cekSemua) {
            cekSemua.addEventListener('change', function () {
                kotak.forEach(function (k) { k.checked = cekSemua.checked; });
                segarkan();
            });
        }

        btnTerpilih.addEventListener('click', function () {
            bukaModal('terpilih');
        });

        btnSemua.addEventListener('click', function () {
            bukaModal('semua');
        });

        formMusnah.addEventListener('submit', function () {
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memproses...';
        });

        segarkan();

        @if ($errors->musnah->any())
            isiRingkasan();
            modalMusnah.show();
        @endif

        const kotakRiwayat = Array.from(document.querySelectorAll('.cek-riwayat'));
        const cekSemuaRiwayat = document.getElementById('cek-semua-riwayat');
        const btnRiwayatTerpilih = document.getElementById('btn-hapus-riwayat-terpilih');
        const btnRiwayatSemua = document.getElementById('btn-hapus-riwayat-semua');
        const jumlahRiwayatTerpilih = document.getElementById('jumlah-riwayat-terpilih');
        const modeRiwayat = document.getElementById('mode-riwayat');
        const formHapusRiwayat = document.getElementById('form-hapus-riwayat-massal');

        const kfOverlay = document.getElementById('kf-overlay');
        const kfPesan = document.getElementById('kf-pesan');
        const kfBatal = document.getElementById('kf-batal');
        const kfYa = document.getElementById('kf-ya');
        let targetFormAktif = null;

        function jumlahRiwayatDipilih() {
            return kotakRiwayat.filter(function (k) { return k.checked; }).length;
        }

        function segarkanRiwayat() {
            if (kotakRiwayat.length === 0) return;
            const n = jumlahRiwayatDipilih();
            if (jumlahRiwayatTerpilih) jumlahRiwayatTerpilih.textContent = n;
            if (btnRiwayatTerpilih) btnRiwayatTerpilih.disabled = n === 0;
            if (cekSemuaRiwayat) {
                cekSemuaRiwayat.checked = n > 0 && n === kotakRiwayat.length;
                cekSemuaRiwayat.indeterminate = n > 0 && n < kotakRiwayat.length;
            }
        }

        kotakRiwayat.forEach(function (k) {
            k.addEventListener('change', segarkanRiwayat);
        });

        if (cekSemuaRiwayat) {
            cekSemuaRiwayat.addEventListener('change', function () {
                kotakRiwayat.forEach(function (k) { k.checked = cekSemuaRiwayat.checked; });
                segarkanRiwayat();
            });
        }

        function tampilkanKonfirmasiKustom(pesanText, formTarget) {
            if (kfOverlay && kfPesan) {
                kfPesan.textContent = pesanText;
                kfOverlay.hidden = false;
                document.body.classList.add('kf-terkunci');
                targetFormAktif = formTarget;
            } else {
                if (confirm(pesanText)) {
                    formTarget.submit();
                }
            }
        }

        if (btnRiwayatTerpilih) {
            btnRiwayatTerpilih.addEventListener('click', function () {
                const n = jumlahRiwayatDipilih();
                if (n > 0) {
                    modeRiwayat.value = 'terpilih';
                    tampilkanKonfirmasiKustom('Hapus ' + n + ' riwayat pemusnahan yang dipilih?', formHapusRiwayat);
                }
            });
        }

        if (btnRiwayatSemua) {
            btnRiwayatSemua.addEventListener('click', function () {
                modeRiwayat.value = 'semua';
                tampilkanKonfirmasiKustom('Apakah Anda yakin ingin menghapus seluruh riwayat pemusnahan?', formHapusRiwayat);
            });
        }

        document.querySelectorAll('form[data-konfirmasi]').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const pesan = form.getAttribute('data-konfirmasi');
                tampilkanKonfirmasiKustom(pesan, form);
            });
        });

        if (kfYa) {
            kfYa.onclick = function() {
                if (targetFormAktif) {
                    targetFormAktif.submit();
                }
            };
        }

        if (kfBatal) {
            kfBatal.onclick = function() {
                if (kfOverlay) {
                    kfOverlay.hidden = true;
                    document.body.classList.remove('kf-terkunci');
                }
                targetFormAktif = null;
            };
        }

        segarkanRiwayat();

        document.querySelectorAll('.alert-otomatis').forEach(function (el) {
            setTimeout(function () {
                if (el.isConnected) {
                    bootstrap.Alert.getOrCreateInstance(el).close();
                }
            }, 5000);
        });
    </script>
    @endpush
</x-dynamic-component>