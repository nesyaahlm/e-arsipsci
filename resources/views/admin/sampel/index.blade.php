<x-admin.layout title="Arsip Sampel">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
        <div>
            <h1 class="h3 fw-bold text-primary mb-1">Arsip Sampel</h1>
            <p class="text-secondary mb-0 small">Cari, ubah, dan kelola seluruh sampel arsip.</p>
        </div>

        <div class="d-flex gap-2">
            <form id="form-ajukan" method="POST" action="{{ route('pemusnahan.ajukan') }}">
                @csrf
                <input type="hidden" name="tipe" value="sampel">
                <button type="submit" id="btn-ajukan" class="btn btn-outline-danger btn-sm fw-semibold px-3 py-2" disabled>
                    Pemusnahan (<span id="jumlah-pilih">0</span>)
                </button>
            </form>

            <a href="{{ route('admin.sampel.create') }}" class="btn btn-primary btn-sm fw-semibold px-3 py-2">
                + Unggah Sampel
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show alert-otomatis rounded-3 small py-2 shadow-sm border-0" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show alert-otomatis rounded-3 small py-2 shadow-sm border-0" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    @if ($errors->has('ids') || $errors->has('tipe'))
        <div class="alert alert-danger alert-dismissible fade show alert-otomatis rounded-3 small py-2 shadow-sm border-0" role="alert">
            {{ $errors->first('ids') ?: $errors->first('tipe') }}
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    <form action="{{ route('admin.sampel.index') }}" method="GET"
          class="card shadow-sm rounded-4 mb-3" style="border: 1.5px solid #cfdedb;">
        <div class="card-body p-3">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-semibold text-secondary mb-1">Cari</label>
                    <input type="text" name="cari" value="{{ request('cari') }}"
                           class="form-control form-control-sm" placeholder="Cari kode, nama, shipper, vessel, keterangan, ruang, pengunggah, dll.">
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label small fw-semibold text-secondary mb-1">Portofolio</label>
                    <select name="portofolio_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Portofolio</option>
                        @foreach ($daftarPortofolio as $portofolio)
                            <option value="{{ $portofolio->id }}" @selected(request('portofolio_id') == $portofolio->id)>
                                {{ $portofolio->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label small fw-semibold text-secondary mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                        <option value="inaktif" @selected(request('status') === 'inaktif')>Inaktif</option>
                        <option value="pemusnahan" @selected(request('status') === 'pemusnahan')>Pemusnahan</option>
                    </select>
                </div>

                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1">Cari</button>
                    <a href="{{ route('admin.sampel.index') }}" class="btn btn-outline-primary btn-sm">Reset</a>
                </div>
            </div>
        </div>
    </form>

    <div class="card shadow-sm rounded-4 overflow-hidden" style="border: 1.5px solid #cfdedb;">
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0 small">
                <thead style="--bs-table-bg: #e3f1ee; --bs-table-color: #1f5f55; font-size: 0.8rem;">
                    <tr>
                        <th class="py-2 ps-3 text-center" style="width: 44px;">
                            <input type="checkbox" id="cek-semua" class="form-check-input" title="Pilih semua untuk pemusnahan">
                        </th>
                        <th class="py-2 text-center fw-bold">Kode Sampel</th>
                        <th class="py-2 text-center fw-bold">Nama Sampel</th>
                        <th class="py-2 text-center fw-bold">Portofolio</th>
                        <th class="py-2 text-center fw-bold">Keterangan</th>
                        <th class="py-2 text-center fw-bold">Status</th>
                        <th class="py-2 text-center fw-bold">Tgl Masuk</th>
                        <th class="py-2 text-center fw-bold">Ruang Arsip</th>
                        <th class="py-2 text-center fw-bold">Diunggah Oleh</th>
                        <th class="py-2 text-center fw-bold pe-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sampel as $item)
                        @php
                            $warnaStatus = [
                                'aktif' => 'bg-success-subtle text-success',
                                'inaktif' => 'bg-secondary-subtle text-secondary',
                                'pemusnahan' => 'bg-danger-subtle text-danger',
                            ][$item->status] ?? 'bg-light text-dark';

                            $daftarKeterangan = collect([$item->keterangan_1, $item->keterangan_2, $item->keterangan_3])
                                ->map(fn ($k) => trim((string) $k))
                                ->filter(fn ($k) => $k !== '' && $k !== '-');
                        @endphp
                        <tr>
                            <td class="ps-3 py-2 text-center">
                                <input type="checkbox" class="form-check-input cek-item" name="ids[]" value="{{ $item->id }}" form="form-ajukan" @disabled($item->status === 'pemusnahan')>
                            </td>

                            <td class="py-2 fw-bold text-dark font-monospace text-nowrap" style="font-size: 0.78rem;">{{ $item->kode_sampel }}</td>

                            <td class="py-2">
                                <a href="{{ route('admin.sampel.show', $item) }}"
                                   class="fw-semibold text-success link-success link-underline link-underline-opacity-75"
                                   title="Lihat detail">{{ $item->nama_sampel }}</a>
                            </td>

                            <td class="py-2 text-center">{{ $item->portofolio->nama ?? $item->bagian->nama ?? '-' }}</td>

                            <td class="py-2" style="font-size: 0.75rem;">
                                @forelse ($daftarKeterangan as $keterangan)
                                    <div>{{ $keterangan }}</div>
                                @empty
                                    <span class="text-muted">-</span>
                                @endforelse
                            </td>

                            <td class="py-2 text-center">
                                <span class="badge {{ $warnaStatus }}">{{ ucfirst($item->status) }}</span>
                            </td>

                            <td class="py-2 text-center text-nowrap">{{ $item->tanggal_masuk_arsip?->format('d/m/Y') }}</td>

                            <td class="py-2" style="font-size: 0.75rem; min-width: 110px;">
                                @if ($item->status === 'pemusnahan')
                                    <span class="text-muted">-</span>
                                @else
                                    <div class="fw-semibold text-dark">{{ $item->nama_ruang_arsip ?: '-' }}</div>
                                    <div class="text-secondary">Rak {{ $item->nomor_ruang_arsip ?: '-' }}</div>
                                    @if (filled($item->nomor_tingkat_arsip))
                                        <div class="text-secondary">Tingkat {{ $item->nomor_tingkat_arsip }}</div>
                                    @endif
                                @endif
                            </td>
                            <td class="py-2">{{ $item->pengunggah->name ?? '-' }}</td>
                            <td class="py-2 text-center pe-3">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('admin.sampel.cetak-label', $item->id) }}"
                                       target="_blank"
                                       class="btn btn-outline-success d-flex align-items-center justify-content-center p-0"
                                       style="width: 30px; height: 30px;" title="Cetak Label">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="6 9 6 2 18 2 18 9"></polyline>
                                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                                            <rect x="6" y="14" width="12" height="8"></rect>
                                        </svg>
                                    </a>

                                    <a href="{{ route('admin.sampel.edit', $item->id) }}"
                                       class="btn btn-outline-primary d-flex align-items-center justify-content-center p-0"
                                       style="width: 30px; height: 30px;" title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 20h9"></path>
                                            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                        </svg>
                                    </a>

                                    <form action="{{ route('admin.sampel.destroy', $item) }}" method="POST"
                                          data-konfirmasi="Sampel {{ $item->kode_sampel }} akan dihapus.">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-outline-danger d-flex align-items-center justify-content-center p-0"
                                                style="width: 30px; height: 30px;" title="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path>
                                                <path d="M10 11v6M14 11v6"></path>
                                                <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">Belum ada sampel.</td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>

    @if ($sampel->hasPages())
        <div class="mt-3 d-flex justify-content-center">
            {{ $sampel->links('pagination::bootstrap-5') }}
        </div>
    @endif

    @push('scripts')
        <script src="{{ asset('js/admin/pemusnahan-pilih.js') }}"></script>
        <script>
            document.querySelectorAll('.alert-otomatis').forEach(function (el) {
                setTimeout(function () {
                    bootstrap.Alert.getOrCreateInstance(el).close();
                }, 5000);
            });
        </script>
    @endpush
</x-admin.layout>