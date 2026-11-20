<x-superadmin.layout title="Dashboard Kantor Pusat">
    <div class="rounded-4 p-4 mb-4 shadow-sm" style="background: linear-gradient(90deg, #9ec8b5 0%, #ccefe0 100%);">
        <div class="d-flex align-items-center gap-2 mb-1">
            <h1 class="h3 fw-bold text-white mb-0">Selamat Datang, {{ auth()->user()->name }}!</h1>
            <span class="badge bg-white text-success fw-bold rounded-pill px-2.5 py-1 small shadow-sm">Kantor Pusat</span>
        </div>
        <p class="text-white mb-3" style="font-size: 0.95rem; opacity: 0.95;">
            Pantau dan kelola arsip dokumen nasional, sampel, standarisasi kode surat, dan pengawasan kantor unit kerja.
        </p>

    <form id="formPencarianDashboard" action="{{ route('superadmin.dokumen.index') }}" method="GET">
            <div class="input-group shadow-sm rounded-pill overflow-hidden bg-white p-1">
                <select id="pilihanKategoriPencarian" class="form-select border-0 ps-3 pe-4 fw-semibold text-primary" style="max-width: 150px; font-size: 0.88rem; background-color: #f0f7f6; border-radius: 50rem 0 0 50rem !important;">
                    <option value="{{ route('superadmin.dokumen.index') }}" selected>Dokumen</option>
                    <option value="{{ route('superadmin.sampel.index') }}">Sampel</option>
                </select>
                <input type="text"
                       id="inputCariDashboard"
                       name="cari"
                       class="form-control border-0 ps-3 py-2"
                       placeholder="Cari arsip nasional (nomor surat, judul dokumen, atau nama sampel)..."
                       style="font-size: 0.92rem; color: #495057;">
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold me-1">
                    Cari
                </button>
            </div>
        </form>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('superadmin.dokumen.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background-color: #f0f7f6; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase">Total Dokumen</div>
                            <div class="h2 fw-bold text-dark mb-0 mt-1">{{ number_format($totalDokumen) }}</div>
                        </div>
                        <div class="d-flex align-items-center justify-content-center rounded-3 p-2.5" style="background-color: #277a72; color: #ffffff;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/></svg>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('superadmin.sampel.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background-color: #f0f7f6; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase">Total Sampel</div>
                            <div class="h2 fw-bold text-dark mb-0 mt-1">{{ number_format($totalSampel) }}</div>
                        </div>
                        <div class="d-flex align-items-center justify-content-center rounded-3 p-2.5" style="background-color: #277a72; color: #ffffff;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('superadmin.bagian.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background-color: #f0f7f6; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase">Bagian / Divisi</div>
                            <div class="h2 fw-bold text-dark mb-0 mt-1">{{ number_format($totalBagian) }}</div>
                        </div>
                        <div class="d-flex align-items-center justify-content-center rounded-3 p-2.5" style="background-color: #277a72; color: #ffffff;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="6" rx="1"/><rect x="3" y="13" width="18" height="8" rx="1"/></svg>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('superadmin.admin-cabang.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background-color: #f0f7f6; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase">Admin Unit Kerja</div>
                            <div class="h2 fw-bold text-dark mb-0 mt-1">{{ number_format($totalAdminCabang) }}</div>
                        </div>
                        <div class="d-flex align-items-center justify-content-center rounded-3 p-2.5" style="background-color: #277a72; color: #ffffff;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <h2 class="h6 fw-bold text-dark mb-3">Distribusi Arsip per Bagian</h2>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead>
                            <tr class="text-muted small">
                                <th>KODE</th>
                                <th>NAMA BAGIAN</th>
                                <th class="text-end">JUMLAH DOKUMEN</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($daftarBagian as $b)
                                <tr>
                                    <td class="font-monospace fw-semibold">{{ $b->kode }}</td>
                                    <td>{{ $b->nama }}</td>
                                    <td class="text-end fw-bold">{{ $b->dokumen_count }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">Belum ada data bagian.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <h2 class="h6 fw-bold text-dark mb-3">Dokumen Masuk Terbaru</h2>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead>
                            <tr class="text-muted small">
                                <th>NOMOR</th>
                                <th>JUDUL</th>
                                <th>BAGIAN</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($dokumenTerbaru as $d)
                                <tr>
                                    <td class="font-monospace small fw-semibold">{{ $d->nomor }}</td>
                                    <td class="text-truncate" style="max-width: 180px;">{{ $d->judul }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $d->bagian->nama ?? '-' }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">Belum ada dokumen.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var selectKategori = document.getElementById('pilihanKategoriPencarian');
                var formCari = document.getElementById('formPencarianDashboard');
                var inputCari = document.getElementById('inputCariDashboard');

                if (selectKategori && formCari) {
                    selectKategori.addEventListener('change', function () {
                        formCari.action = this.value;
                        if (this.value.includes('sampel')) {
                            inputCari.placeholder = 'Cari arsip sampel (kode sampel, nama sampel, shipper, vessel)...';
                        } else {
                            inputCari.placeholder = 'Cari arsip dokumen (nomor surat, judul dokumen, no buku)...';
                        }
                    });
                }
            });
        </script>
    @endpush
</x-superadmin.layout>