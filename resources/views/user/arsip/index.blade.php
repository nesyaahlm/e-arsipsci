@push('styles')
    <link rel="stylesheet" href="{{ asset('css/user/dokumen-saya.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user/arsip.css') }}">
@endpush

<x-layouts.user title="Cari Arsip">
    <div class="ua-page-header">
        <h1 class="ua-page-title">Cari Arsip</h1>
        <p class="ua-page-subtitle">Temukan dokumen arsip berdasarkan judul, nomor, isi, atau lokasi box.</p>
    </div>

    <form method="GET" action="{{ route('user.arsip.index') }}" class="ar-form" role="search">
        <div class="dk-search">
            <div class="dk-search__field">
                <x-user.icon name="magnify" :size="20" class="dk-search__icon" />
                <input type="search" name="q" value="{{ $kata }}" maxlength="150" class="dk-search__input" placeholder="Cari judul, nomor, isi, kode rak, unit kerja..." aria-label="Kata kunci pencarian" autofocus>
            </div>
            <button type="submit" class="ua-btn ua-btn--primary dk-search__btn">Cari</button>
        </div>

        <details class="ar-filter" @if ($adaFilter) open @endif>
            <summary class="ar-filter__toggle">
                Filter
                @if ($adaFilter)
                    <span class="ar-filter__dot" aria-label="Filter aktif"></span>
                @endif
            </summary>

            <div class="ar-filter__grid">
                <label class="ar-field">
                    <span class="ar-field__label">Layanan / Portofolio</span>
                    <select name="layanan_id" class="ar-select">
                        <option value="">Semua layanan</option>
                        @foreach ($layananList as $layanan)
                            <option value="{{ $layanan->id }}" @selected($filter['layanan_id'] === $layanan->id)>{{ $layanan->nama }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="ar-field">
                    <span class="ar-field__label">Jenis Dokumen</span>
                    <select name="jenis_dokumen_id" class="ar-select">
                        <option value="">Semua jenis</option>
                        @foreach ($jenisList as $jenis)
                            <option value="{{ $jenis->id }}" @selected($filter['jenis_dokumen_id'] === $jenis->id)>{{ $jenis->nama }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="ar-field">
                    <span class="ar-field__label">Tahun</span>
                    <select name="tahun" class="ar-select">
                        <option value="">Semua tahun</option>
                        @foreach ($tahunList as $tahun)
                            <option value="{{ $tahun }}" @selected($filter['tahun'] === $tahun)>{{ $tahun }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="ar-field">
                    <span class="ar-field__label">Unit Kerja</span>
                    <select name="unit" class="ar-select">
                        <option value="">Semua unit</option>
                        @foreach ($unitList as $unit)
                            <option value="{{ $unit }}" @selected($filter['unit'] === $unit)>{{ $unit }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="ar-field">
                    <span class="ar-field__label">Urutkan</span>
                    <select name="urut" class="ar-select">
                        <option value="relevansi" @selected($filter['urut'] === 'relevansi')>Paling relevan</option>
                        <option value="terbaru" @selected($filter['urut'] === 'terbaru')>Terbaru</option>
                    </select>
                </label>

                <div class="ar-filter__actions">
                    <button type="submit" class="ua-btn ua-btn--primary">Terapkan</button>
                    <a href="{{ route('user.arsip.index') }}" class="ua-btn ua-btn--outline">Reset Filter</a>
                </div>
            </div>
        </details>
    </form>

    <p class="dk-count">
        {{ number_format($hasil->total(), 0, ',', '.') }} arsip ditemukan
        @if ($kata !== '')
            untuk kata kunci "{{ $kata }}"
        @endif
    </p>

    @if ($hasil->isEmpty())
        <div class="ua-card ar-empty">
            <x-user.icon name="magnify" :size="30" />
            <strong class="ar-empty__title">Arsip tidak ditemukan</strong>
            <span class="ar-empty__text">
                @if ($kata !== '' || $adaFilter)
                    Coba gunakan kata kunci lain atau kurangi filter yang dipilih.
                @else
                    Belum ada dokumen yang tersedia di arsip.
                @endif
            </span>
            @if ($kata !== '' || $adaFilter)
                <a href="{{ route('user.arsip.index') }}" class="ua-btn ua-btn--outline">Reset Filter</a>
            @endif
        </div>
    @else
        <div class="ar-list">
            @foreach ($hasil as $item)
                <article class="ua-card ar-item">
                    <div class="ar-item__main">
                        <a href="{{ route('user.arsip.show', $item['id']) }}" class="ar-item__title">{{ $item['judul'] }}</a>
                        <span class="ar-item__nomor">{{ $item['nomor'] }}</span>

                        <div class="ar-item__tags">
                            <span class="ar-tag">{{ $item['jenis'] }}</span>
                            <span class="ar-tag ar-tag--soft">{{ $item['layanan'] }}</span>
                        </div>

                        <dl class="ar-item__meta">
                            <div><dt>Unit Kerja</dt><dd>{{ filled($item['unit']) ? $item['unit'] : '-' }}</dd></div>
                            <div><dt>Tahun</dt><dd>{{ $item['tahun'] }}</dd></div>
                            <div><dt>Diunggah</dt><dd>{{ $item['diunggah'] }}</dd></div>
                        </dl>

                        <div class="ar-item__lokasi">
                            @if ($item['slot_terisi'])
                                <div class="dk-slots">
                                    @foreach ($item['slot'] as $nama => $nilai)
                                        <span class="dk-slot">
                                            <span class="dk-slot__name">{{ $nama }}</span>
                                            <strong class="dk-slot__value">{{ filled($nilai) ? $nilai : '-' }}</strong>
                                        </span>
                                    @endforeach
                                </div>
                            @elseif (filled($item['lokasi_lama']))
                                <span class="ar-lokasi-teks">Lokasi: {{ $item['lokasi_lama'] }}</span>
                            @else
                                <span class="ar-lokasi-teks is-empty">Lokasi box belum dicatat</span>
                            @endif
                        </div>
                    </div>

                    <a href="{{ route('user.arsip.show', $item['id']) }}" class="ua-btn ua-btn--outline ar-item__btn">Lihat Detail</a>
                </article>
            @endforeach
        </div>

        <x-user.pagination :paginator="$hasil" />
    @endif
</x-layouts.user>