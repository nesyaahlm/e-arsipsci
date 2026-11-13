@push('styles')
    <link rel="stylesheet" href="{{ asset('css/user/dokumen-saya.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user/arsip.css') }}">
@endpush

@php
    $adaPencarian = $kata !== '' || $adaFilter;
    $namaKategori = ['semua' => 'arsip', 'dokumen' => 'dokumen', 'sampel' => 'sampel'][$kategori];
    $urlResetFilter = route('user.arsip.index', ['q' => $kata !== '' ? $kata : null, 'kategori' => $kategori]);

    if ($adaPencarian) {
        $judulKosong = ['semua' => 'Arsip tidak ditemukan', 'dokumen' => 'Dokumen tidak ditemukan', 'sampel' => 'Sampel tidak ditemukan'][$kategori];
        $teksKosong = 'Coba gunakan kata kunci lain atau kurangi filter yang dipilih.';
    } else {
        $judulKosong = ['semua' => 'Belum ada arsip', 'dokumen' => 'Belum ada dokumen', 'sampel' => 'Belum ada sampel'][$kategori];
        $teksKosong = [
            'semua' => 'Belum ada dokumen maupun sampel yang tersedia untuk dicari.',
            'dokumen' => 'Belum ada dokumen yang tersedia di arsip.',
            'sampel' => 'Anda belum memiliki sampel. Tambahkan sampel dari menu Sampel Saya.',
        ][$kategori];
    }
@endphp

<x-layouts.user title="Cari Arsip">
    <div class="ua-page-header">
        <h1 class="ua-page-title">Cari Arsip</h1>
        <p class="ua-page-subtitle">Temukan dokumen dan sampel arsip berdasarkan judul, nomor, isi, atau lokasi penyimpanan.</p>
    </div>

    <form method="GET" action="{{ route('user.arsip.index') }}" class="ar-form" role="search">
        <div class="dk-search">
            <div class="dk-search__field">
                <x-user.icon name="magnify" :size="20" class="dk-search__icon" />
                <input type="search" name="q" value="{{ $kata }}" maxlength="150" class="dk-search__input" placeholder="Cari judul, nomor, nama sampel, isi, lokasi..." aria-label="Kata kunci pencarian" autofocus>
            </div>
            <button type="submit" class="ua-btn ua-btn--primary dk-search__btn">Cari</button>
        </div>

        <div class="ar-kategori" role="radiogroup" aria-label="Kategori arsip">
            @foreach (['semua' => 'Semua', 'dokumen' => 'Dokumen', 'sampel' => 'Sampel'] as $nilai => $label)
                <label class="ar-kategori__item">
                    <input type="radio" name="kategori" value="{{ $nilai }}" class="ar-kategori__input" @checked($kategori === $nilai) onchange="this.form.submit()">
                    <span class="ar-kategori__label">{{ $label }}</span>
                </label>
            @endforeach
        </div>

        <div class="ar-filter-bar">
            <label class="ar-field">
                <span class="ar-field__label">Layanan</span>
                <select name="layanan_id" class="ar-select" onchange="this.form.submit()">
                    <option value="">Semua layanan</option>
                    @foreach ($layananList as $layanan)
                        <option value="{{ $layanan->id }}" @selected($filter['layanan_id'] === $layanan->id)>{{ $layanan->nama }}</option>
                    @endforeach
                </select>
            </label>

            @if ($kategori === 'dokumen')
                <label class="ar-field">
                    <span class="ar-field__label">Jenis Dokumen</span>
                    <select name="jenis_dokumen_id" class="ar-select" onchange="this.form.submit()">
                        <option value="">Semua jenis</option>
                        @foreach ($jenisList as $jenis)
                            <option value="{{ $jenis->id }}" @selected($filter['jenis_dokumen_id'] === $jenis->id)>{{ $jenis->nama }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="ar-field">
                    <span class="ar-field__label">Tahun</span>
                    <select name="tahun" class="ar-select" onchange="this.form.submit()">
                        <option value="">Semua tahun</option>
                        @foreach ($tahunList as $tahun)
                            <option value="{{ $tahun }}" @selected($filter['tahun'] === $tahun)>{{ $tahun }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="ar-field">
                    <span class="ar-field__label">Unit Kerja</span>
                    <select name="unit" class="ar-select" onchange="this.form.submit()">
                        <option value="">Semua unit</option>
                        @foreach ($unitList as $unit)
                            <option value="{{ $unit }}" @selected($filter['unit'] === $unit)>{{ $unit }}</option>
                        @endforeach
                    </select>
                </label>
            @endif

            <label class="ar-field">
                <span class="ar-field__label">Urutkan</span>
                <select name="urut" class="ar-select" onchange="this.form.submit()">
                    <option value="relevansi" @selected($filter['urut'] === 'relevansi')>Paling relevan</option>
                    <option value="terbaru" @selected($filter['urut'] === 'terbaru')>Terbaru</option>
                </select>
            </label>

            @if ($adaFilter)
                <a href="{{ $urlResetFilter }}" class="ua-btn ua-btn--outline ar-filter-bar__reset">Reset</a>
            @endif

            <noscript>
                <button type="submit" class="ua-btn ua-btn--primary">Terapkan</button>
            </noscript>
        </div>
    </form>

    <p class="dk-count">
        {{ number_format($hasil->total(), 0, ',', '.') }} {{ $namaKategori }} ditemukan
        @if ($kata !== '')
            untuk kata kunci "{{ $kata }}"
        @endif
    </p>

    @if ($hasil->isEmpty())
        <div class="ua-card ar-empty">
            <x-user.icon name="magnify" :size="30" />
            <strong class="ar-empty__title">{{ $judulKosong }}</strong>
            <span class="ar-empty__text">{{ $teksKosong }}</span>
            @if ($adaPencarian)
                <a href="{{ route('user.arsip.index', ['kategori' => $kategori]) }}" class="ua-btn ua-btn--outline">Reset Pencarian</a>
            @endif
        </div>
    @else
        <div class="ar-list">
            @foreach ($hasil as $item)
                @if ($item['tipe'] === 'sampel')
                    <article class="ua-card ar-item">
                        <div class="ar-item__main">
                            <span class="ar-jenis ar-jenis--sampel">Sampel</span>
                            <a href="{{ route('user.sampel.show', $item['id']) }}" class="ar-item__title">{{ $item['nama'] }}</a>
                            <span class="ar-item__nomor">{{ $item['kode'] }}</span>

                            <div class="ar-item__tags">
                                <span class="ar-tag">{{ $item['layanan'] }}</span>
                            </div>

                            <dl class="ar-item__meta">
                                <div><dt>Shipper</dt><dd>{{ filled($item['shipper']) ? $item['shipper'] : '-' }}</dd></div>
                                <div><dt>Tujuan Analisis</dt><dd>{{ $item['tujuan'] }}</dd></div>
                                <div><dt>Diunggah</dt><dd>{{ $item['diunggah'] }}</dd></div>
                            </dl>

                            <div class="ar-item__lokasi">
                                <span class="ar-lokasi-teks">Ruang arsip: {{ $item['ruang'] }}</span>
                            </div>
                        </div>

                        <a href="{{ route('user.sampel.show', $item['id']) }}" class="ua-btn ua-btn--outline ar-item__btn">Lihat Detail</a>
                    </article>
                @else
                    <article class="ua-card ar-item">
                        <div class="ar-item__main">
                            <span class="ar-jenis ar-jenis--dokumen">Dokumen</span>
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
                @endif
            @endforeach
        </div>

        <x-user.pagination :paginator="$hasil" />
    @endif
</x-layouts.user>