<x-layouts.user title="Dokumen Saya">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/user/dokumen-saya.css') }}">
    @endpush

    <div class="ua-page-header">
        <h1 class="ua-page-title">Dokumen Saya</h1>
        <p class="ua-page-subtitle">Daftar dokumen yang saya unggah. Klik judul untuk melihat detail dan label box arsip.</p>
    </div>

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

    <p class="dk-count">
        {{ $dokumen->total() }} dokumen
        @if ($kata !== '')
            untuk kata kunci "{{ $kata }}"
        @endif
    </p>

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
                            <th>Status</th>
                            <th>Catatan Admin</th>
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
                                        <div class="dk-slots">
                                            @foreach (['Kode Rak' => 'kode_rak', 'No. Rak' => 'nomor_rak', 'Tingkat' => 'no_tingkat', 'Box' => 'no_box_file'] as $nama => $kunci)
                                                <span class="dk-slot">
                                                    <span class="dk-slot__name">{{ $nama }}</span>
                                                    <strong class="dk-slot__value">{{ filled($arsip[$kunci]) ? $arsip[$kunci] : '-' }}</strong>
                                                </span>
                                            @endforeach
                                        </div>
                                    @elseif (filled($arsip['lokasi_lama']))
                                        {{ $arsip['lokasi_lama'] }}
                                    @else
                                        <span class="is-empty">-</span>
                                    @endif
                                </td>
                                <td data-label="Tgl Unggah">{{ $arsip['diunggah_singkat'] }}</td>
                                <td data-label="Status">
                                    <span class="ua-badge ua-badge--{{ $arsip['status'] }}">{{ $arsip['status_label'] }}</span>
                                </td>
                                <td data-label="Catatan Admin">{{ filled($arsip['catatan']) ? $arsip['catatan'] : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <x-user.pagination :paginator="$dokumen" />
    @endif
</x-layouts.user>