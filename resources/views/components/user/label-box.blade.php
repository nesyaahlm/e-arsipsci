@props(['arsip', 'compact' => false])
@php
    $lokasi = [
        'Kode Rak' => $arsip['kode_rak'],
        'Nomor Rak' => $arsip['nomor_rak'],
        'No. Tingkat' => $arsip['no_tingkat'],
        'No. Box File' => $arsip['no_box_file'],
    ];
    $baris = [
        'Unit Kerja' => $arsip['unit_kerja'],
        'Portofolio' => $arsip['portofolio'],
        'Tahun' => $arsip['tahun'],
    ];
@endphp
<div class="dk-label {{ $compact ? 'dk-label--compact' : '' }}">
    <div class="dk-label__top">
        @foreach ($lokasi as $nama => $nilai)
            <div class="dk-cell">
                <span class="dk-cell__name">{{ $nama }}</span>
                <strong class="dk-cell__value {{ filled($nilai) ? '' : 'is-empty' }}">{{ filled($nilai) ? $nilai : '-' }}</strong>
            </div>
        @endforeach
    </div>

    @foreach ($baris as $nama => $nilai)
        <div class="dk-label__row">
            <span class="dk-label__name">{{ $nama }}</span>
            <span class="dk-label__value {{ filled($nilai) ? '' : 'is-empty' }}">{{ filled($nilai) ? $nilai : '-' }}</span>
        </div>
    @endforeach

    <div class="dk-label__row dk-label__row--isi">
        <span class="dk-label__name">Isi</span>
        <span class="dk-label__value">
            <strong class="dk-isi__title">{{ $arsip['judul'] }}</strong>
            <small class="dk-isi__nomor">{{ $arsip['nomor'] }}</small>
        </span>
    </div>
</div>