@push('styles')
    <link rel="stylesheet" href="{{ asset('css/user/dokumen-saya.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user/arsip.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user/unggah.css') }}">
    <style>
        #tambah-sampel .ug-modal__dialog {
            max-width: 860px;
        }

        #tambah-sampel .ug-title {
            margin-bottom: 12px;
            font-size: 16px;
        }

        #tambah-sampel .card {
            margin-bottom: 12px !important;
        }

        #tambah-sampel .card-body {
            padding: 16px !important;
        }

        #tambah-sampel h5 {
            font-size: 15px;
        }

        #tambah-sampel .row {
            --bs-gutter-x: 0.75rem;
            --bs-gutter-y: 0.75rem;
        }

        #tambah-sampel .form-label {
            margin-bottom: 4px;
            font-size: 12.5px;
        }

        #tambah-sampel .form-control,
        #tambah-sampel .form-select {
            padding: 5px 10px;
            font-size: 14px;
        }

        #tambah-sampel .form-control:focus,
        #tambah-sampel .form-select:focus {
            border-color: #7fbfb5;
            box-shadow: 0 0 0 0.2rem rgba(42, 127, 114, 0.25);
        }

        #tambah-sampel .small {
            font-size: 12.5px;
        }

        #tambah-sampel .d-flex.justify-content-end {
            margin-bottom: 0 !important;
        }

        #tambah-sampel .btn {
            padding: 6px 18px !important;
            font-size: 14px;
        }

        #tambah-sampel .btn-primary {
            --bs-btn-bg: #2a7f72;
            --bs-btn-border-color: #2a7f72;
            --bs-btn-hover-bg: #236b60;
            --bs-btn-hover-border-color: #236b60;
            --bs-btn-active-bg: #1f5d54;
            --bs-btn-active-border-color: #1f5d54;
            --bs-btn-disabled-bg: #2a7f72;
            --bs-btn-disabled-border-color: #2a7f72;
            --bs-btn-focus-shadow-rgb: 42, 127, 114;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('js/user/unggah.js') }}" defer></script>
@endpush

<x-layouts.user title="Sampel Saya">
    <div class="ua-page-header dk-head">
        <div>
            <h1 class="ua-page-title">Sampel Saya</h1>
            <p class="ua-page-subtitle">Daftar sampel yang saya unggah. Klik nama sampel untuk melihat detail.</p>
        </div>
        <button type="button" class="ua-btn ua-btn--primary" data-modal-open="tambah-sampel">Tambah Sampel</button>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show alert-otomatis" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    <div class="ug-modal" id="tambah-sampel" data-modal @if ($errors->any() || session('error')) data-open @else hidden @endif>
        <div class="ug-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="judul-tambah-sampel">
            <button type="button" class="ug-modal__close" data-modal-close aria-label="Tutup">&times;</button>
            <div class="p-3 p-md-4">
                <h2 class="ug-title" id="judul-tambah-sampel">Catat Sampel Baru</h2>
                @include('admin.sampel._form', [
                    'aksi' => route('user.sampel.store'),
                    'metode' => 'POST',
                    'sampel' => null,
                    'kembali' => route('user.sampel.index'),
                    'layananList' => $layananList,
                ])
            </div>
        </div>
    </div>

    <form method="GET" action="{{ route('user.sampel.index') }}" class="dk-search" role="search">
        <div class="dk-search__field">
            <x-user.icon name="magnify" :size="20" class="dk-search__icon" />
            <input type="search" name="q" value="{{ $kata }}" class="dk-search__input" placeholder="Cari kode, nama sampel, atau tujuan analisis..." aria-label="Cari sampel">
        </div>
        <button type="submit" class="ua-btn ua-btn--primary dk-search__btn">Cari</button>
        @if ($kata !== '')
            <a href="{{ route('user.sampel.index') }}" class="ua-btn ua-btn--outline dk-search__btn">Reset</a>
        @endif
    </form>

    <p class="dk-count">
        {{ number_format($sampel->total(), 0, ',', '.') }} sampel
        @if ($kata !== '')
            untuk kata kunci "{{ $kata }}"
        @endif
    </p>

    @if ($sampel->isEmpty())
        <div class="ua-card ar-empty">
            <x-user.icon name="box" :size="30" />
            <strong class="ar-empty__title">
                {{ $kata !== '' ? 'Sampel tidak ditemukan' : 'Belum ada sampel' }}
            </strong>
            <span class="ar-empty__text">
                @if ($kata !== '')
                    Coba gunakan kata kunci lain.
                @else
                    Anda belum mengunggah sampel apa pun. Klik Tambah Sampel untuk mencatat sampel pertama.
                @endif
            </span>
            @if ($kata !== '')
                <a href="{{ route('user.sampel.index') }}" class="ua-btn ua-btn--outline">Reset</a>
            @else
                <button type="button" class="ua-btn ua-btn--primary" data-modal-open="tambah-sampel">Tambah Sampel</button>
            @endif
        </div>
    @else
        <div class="ua-card dk-tablecard">
            <div class="ua-table-wrap">
                <table class="dk-table">
                    <thead>
                        <tr>
                            <th>Sampel</th>
                            <th>Layanan</th>
                            <th>Shipper</th>
                            <th>Tujuan Analisis</th>
                            <th>Ruang Arsip</th>
                            <th>Tgl Unggah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sampel as $item)
                            <tr>
                                <td class="dk-table__judul">
                                    <a href="{{ route('user.sampel.show', $item->id) }}">{{ $item->nama_sampel }}</a>
                                    <small>{{ $item->kode_sampel }}</small>
                                </td>
                                <td data-label="Layanan">{{ $item->layanan?->nama ?? '-' }}</td>
                                <td data-label="Shipper">{{ filled($item->shipper) ? $item->shipper : '-' }}</td>
                                <td data-label="Tujuan Analisis">{{ $item->analysis_for }}</td>
                                <td data-label="Ruang Arsip">{{ $item->nama_ruang_arsip }} (No. {{ $item->nomor_ruang_arsip }})</td>
                                <td data-label="Tgl Unggah">{{ $item->created_at->copy()->setTimezone('Asia/Jakarta')->locale('id')->translatedFormat('d M Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <x-user.pagination :paginator="$sampel" />
    @endif
</x-layouts.user>