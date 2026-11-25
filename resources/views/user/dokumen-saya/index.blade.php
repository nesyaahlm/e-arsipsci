<x-layouts.user title="Dokumen Saya">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/user/dokumen-saya.css') }}">
        <link rel="stylesheet" href="{{ asset('css/user/unggah.css') }}">
        <style>
            .dk-label-btn--ikon {
                width: 34px;
                height: 34px;
                padding: 0;
                justify-content: center;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="{{ asset('js/user/unggah.js') }}" defer></script>
        <script>
            (function () {
                var form = document.querySelector('[data-label-form]');

                if (!form) {
                    return;
                }

                var master = form.querySelector('[data-label-semua-halaman]');
                var items = Array.prototype.slice.call(form.querySelectorAll('[data-label-item]:not(:disabled)'));
                var button = document.querySelector('[data-label-terpilih]');
                var badge = document.querySelector('[data-label-jumlah]');
                var tombolMusnah = document.querySelector('[data-pemusnahan-terpilih]');
                var jumlahMusnah = document.querySelector('[data-pemusnahan-jumlah]');

                function sync() {
                    var picked = items.filter(function (box) { return box.checked; }).length;

                    items.forEach(function (box) {
                        var row = box.closest('tr');
                        if (row) {
                            row.classList.toggle('is-picked', box.checked);
                        }
                    });

                    if (master) {
                        master.checked = picked > 0 && picked === items.length;
                        master.indeterminate = picked > 0 && picked < items.length;
                    }

                    if (button) {
                        button.disabled = picked === 0;
                    }

                    if (badge) {
                        badge.textContent = picked;
                    }

                    if (tombolMusnah) {
                        tombolMusnah.disabled = picked === 0;
                    }

                    if (jumlahMusnah) {
                        jumlahMusnah.textContent = picked;
                    }
                }

                if (master) {
                    master.addEventListener('change', function () {
                        items.forEach(function (box) {
                            if (!box.disabled) {
                                box.checked = master.checked;
                            }
                        });
                        sync();
                    });
                }

                items.forEach(function (box) {
                    box.addEventListener('change', sync);
                });

                window.addEventListener('pageshow', sync);
                sync();
            })();
        </script>
    @endpush

    <div class="ua-page-header dk-head">
        <div>
            <h1 class="ua-page-title">Dokumen Saya</h1>
            <p class="ua-page-subtitle">Daftar dokumen yang saya unggah. Klik judul untuk melihat detail dan label box arsip.</p>
        </div>
        <button type="button" class="ua-btn ua-btn--primary" data-modal-open="unggah">Tambah Dokumen</button>
    </div>

    @include('user.unggah._form')

    @if (session('label_error'))
        <div class="alert alert-danger alert-dismissible fade show alert-otomatis" role="alert">
            {{ session('label_error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    @if ($errors->has('ids') || $errors->has('tipe'))
        <div class="alert alert-danger alert-dismissible fade show alert-otomatis" role="alert">
            {{ $errors->first('ids') ?: $errors->first('tipe') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    <form method="GET" action="{{ route('user.dokumen-saya.index') }}" class="dk-search" role="search">
        <div class="dk-search__field">
            <x-user.icon name="magnify" :size="20" class="dk-search__icon" />
            <input type="search" name="q" value="{{ $kata }}" class="dk-search__input" placeholder="Cari judul atau nomor dokumen..." aria-label="Cari dokumen">
        </div>
        <button type="submit" class="ua-btn ua-btn--primary dk-search__btn">Cari</button>
        @if ($kata !== '')
            <a href="{{ route('user.dokumen-saya.index') }}" class="ua-btn ua-btn--outline dk-search__btn">Reset</a>
        @endif
    </form>

    <div class="dk-bar">
        <p class="dk-count">
            {{ $dokumen->total() }} dokumen
            @if ($kata !== '')
                untuk kata kunci "{{ $kata }}"
            @endif
        </p>

        <div class="dk-bar__group">
            @if ($jumlahLabel > 0)
                <button type="submit" form="form-label-terpilih" class="dk-bar__btn dk-bar__btn--outline" data-label-terpilih disabled title="Unduh label hanya untuk dokumen yang dicentang">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    <span>Unduh Terpilih</span>
                    <span class="dk-bar__badge" data-label-jumlah>0</span>
                </button>
                <a href="{{ route('user.dokumen-saya.label-box-semua', array_filter(['q' => $kata])) }}" class="dk-bar__btn" title="Unduh label semua dokumen yang punya lokasi box dalam satu file PDF">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    <span>Download Semua Label</span>
                    <span class="dk-bar__badge">{{ min($jumlahLabel, 100) }}</span>
                </a>
            @endif

            <button type="submit" form="form-label-terpilih" formaction="{{ route('pemusnahan.ajukan') }}" class="dk-bar__btn dk-bar__btn--outline" data-pemusnahan-terpilih disabled title="Masukkan dokumen yang dicentang ke daftar pemusnahan">
                <span>Pemusnahan</span>
                <span class="dk-bar__badge" data-pemusnahan-jumlah>0</span>
            </button>
        </div>
    </div>

    @if ($dokumen->isEmpty())
        <p class="dk-none">
            @if ($kata !== '')
                Tidak ada dokumen yang cocok dengan pencarian Anda.
            @else
                Anda belum mengunggah dokumen apa pun.
            @endif
        </p>
    @else
        <form method="POST" action="{{ route('user.dokumen-saya.label-box-terpilih') }}" id="form-label-terpilih" data-label-form>
            @csrf
            <input type="hidden" name="tipe" value="dokumen">
        <div class="ua-card dk-tablecard">
            <div class="ua-table-wrap">
                <table class="dk-table">
                    <thead>
                        <tr>
                            <th class="dk-check">
                                <input type="checkbox" class="dk-checkbox" data-label-semua-halaman aria-label="Pilih semua dokumen di halaman ini" title="Pilih semua di halaman ini">
                            </th>
                            <th>Judul</th>
                            <th>Bagian</th>
                            <th>Status</th>
                            <th>Lokasi Box</th>
                            <th>Label</th>
                            <th>Tgl Unggah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dokumen as $arsip)
                            @php
                                $statusArsip = $arsip['status'] ?? 'aktif';
                                $warnaStatus = [
                                    'aktif' => 'bg-success-subtle text-success',
                                    'inaktif' => 'bg-secondary-subtle text-secondary',
                                    'pemusnahan' => 'bg-danger-subtle text-danger',
                                ][$statusArsip] ?? 'bg-light text-dark';
                            @endphp
                            <tr>
                                <td class="dk-check" data-label="Pilih">
                                    <input type="checkbox" class="dk-checkbox" name="ids[]" value="{{ $arsip['id'] }}" data-label-item aria-label="Pilih dokumen {{ $arsip['nomor'] }}" @disabled($statusArsip === 'pemusnahan')>
                                </td>
                                <td class="dk-table__judul">
                                    <a href="{{ route('user.dokumen-saya.show', $arsip['id']) }}">{{ $arsip['judul'] }}</a>
                                    <small>{{ $arsip['nomor'] }}</small>
                                </td>
                                <td data-label="Bagian">{{ filled($arsip['portofolio']) ? $arsip['portofolio'] : '-' }}</td>
                                <td data-label="Status">
                                    <span class="badge {{ $warnaStatus }}">{{ ucfirst($statusArsip) }}</span>
                                </td>
                                <td data-label="Lokasi Box">
                                    @if ($arsip['lokasi_terisi'])
                                        <div class="dk-slots">
                                            @foreach (['No. Rak' => 'nomor_rak', 'Tingkat' => 'no_tingkat', 'Box' => 'no_box_file'] as $nama => $kunci)
                                                <span class="dk-slot">
                                                    <span class="dk-slot__name">{{ $nama }}</span>
                                                    <strong class="dk-slot__value">{{ filled($arsip[$kunci]) ? $arsip[$kunci] : '-' }}</strong>
                                                </span>
                                            @endforeach
                                        </div>
                                    @elseif ($statusArsip === 'pemusnahan' && filled($arsip['lokasi_semula']))
                                        <span class="d-block text-muted text-decoration-line-through">{{ $arsip['lokasi_semula'] }}</span>
                                        <small class="d-block text-muted">Lokasi semula</small>
                                    @elseif (filled($arsip['lokasi_lama']) && $arsip['lokasi_lama'] !== '-')
                                        {{ $arsip['lokasi_lama'] }}
                                    @else
                                        <span class="is-empty">-</span>
                                    @endif
                                </td>
                                <td data-label="Label">
                                    @if ($arsip['lokasi_terisi'])
                                        <a href="{{ route('user.dokumen-saya.label-box', $arsip['id']) }}" class="dk-label-btn dk-label-btn--ikon" title="Download label box dokumen ini" aria-label="Download label box dokumen {{ $arsip['nomor'] }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                        </a>
                                    @else
                                        <span class="is-empty">-</span>
                                    @endif
                                </td>
                                <td data-label="Tgl Unggah">{{ $arsip['diunggah_singkat'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        </form>

        <x-user.pagination :paginator="$dokumen" />
    @endif
</x-layouts.user>