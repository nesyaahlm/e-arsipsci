<x-layouts.user title="Dokumen Saya">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/user/dokumen-saya.css') }}">
        <link rel="stylesheet" href="{{ asset('css/user/unggah.css') }}">
    @endpush

    @push('scripts')
        <script src="{{ asset('js/user/unggah.js') }}" defer></script>
    @endpush

    <div class="ua-page-header dk-head">
        <div>
            <h1 class="ua-page-title">Dokumen Saya</h1>
            <p class="ua-page-subtitle">Daftar dokumen yang saya unggah. Klik judul untuk melihat detail dan label box arsip.</p>
        </div>
        <button type="button" class="ua-btn ua-btn--primary" data-modal-open="unggah">Tambah Dokumen</button>
    </div>

    @include('user.unggah._form')

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

        @if ($jumlahLabel > 0)
            <a href="{{ route('user.dokumen-saya.label-box-semua', array_filter(['q' => $kata])) }}" class="dk-bar__btn" title="Unduh label semua dokumen yang punya lokasi box dalam satu file PDF">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                <span>Download Semua Label</span>
                <span class="dk-bar__badge">{{ min($jumlahLabel, 100) }}</span>
            </a>
        @endif
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
        <div class="ua-card dk-tablecard">
            <div class="ua-table-wrap">
                <table class="dk-table">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Jenis</th>
                            <th>Lokasi Box</th>
                            <th>Tgl Unggah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dokumen as $arsip)
                            <tr>
                                <td class="dk-table__judul">
                                    <a href="{{ route('user.dokumen-saya.show', $arsip['id']) }}">{{ $arsip['judul'] }}</a>
                                    <small>{{ $arsip['nomor'] }}</small>
                                </td>
                                <td data-label="Jenis">{{ $arsip['jenis'] }}</td>
                                <td data-label="Lokasi Box">
                                    @if ($arsip['lokasi_terisi'])
                                        <div class="dk-lokasi">
                                            <div class="dk-slots">
                                                @foreach (['No. Rak' => 'nomor_rak', 'Tingkat' => 'no_tingkat', 'Box' => 'no_box_file'] as $nama => $kunci)
                                                    <span class="dk-slot">
                                                        <span class="dk-slot__name">{{ $nama }}</span>
                                                        <strong class="dk-slot__value">{{ filled($arsip[$kunci]) ? $arsip[$kunci] : '-' }}</strong>
                                                    </span>
                                                @endforeach
                                            </div>
                                            <a href="{{ route('user.dokumen-saya.label-box', $arsip['id']) }}" class="dk-label-btn">Download Label Box</a>
                                        </div>
                                    @elseif (filled($arsip['lokasi_lama']))
                                        {{ $arsip['lokasi_lama'] }}
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

        <x-user.pagination :paginator="$dokumen" />
    @endif
</x-layouts.user>x