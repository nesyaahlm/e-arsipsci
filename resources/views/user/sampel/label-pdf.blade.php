@php
    $ukuranNilai = function ($nilai) {
        $panjang = mb_strlen((string) $nilai);

        return $panjang > 8 ? 18 : ($panjang > 4 ? 26 : 38);
    };

    $berkasLogo = public_path('assets/sucofindo-nobg.png');
    $logo = is_file($berkasLogo)
        ? 'data:image/png;base64,'.base64_encode(file_get_contents($berkasLogo))
        : null;

@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Label Sampel Arsip</title>
    <style>
        @page { margin: 10mm; }
        body { margin: 0; font-family: 'DejaVu Sans', sans-serif; color: #1f3f3b; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .frame { border: 3px solid #277a72; page-break-inside: avoid; }
        .head td { background: #ffffff; padding: 8px 14px; vertical-align: middle; border-bottom: 3px solid #277a72; }
        .logo { height: 58px; }
        .brand { font-size: 15pt; font-weight: bold; letter-spacing: 1px; color: #277a72; }
        .sys { font-size: 13pt; font-weight: bold; text-align: right; color: #277a72; }
        .sys-sub { font-size: 8pt; text-align: right; color: #3e8f88; }
        .cap { background: #aeefd5; color: #277a72; font-size: 7.5pt; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; padding: 5px 14px; border-top: 1px solid #277a72; }
        .judul { font-size: 15pt; font-weight: bold; padding: 12px 14px; line-height: 1.3; word-wrap: break-word; }
        .info td { vertical-align: top; padding: 0; border-top: 1px solid #277a72; }
        .info td + td { border-left: 1px solid #277a72; }
        .info-val { font-size: 12pt; font-weight: bold; padding: 8px 14px 10px 14px; word-wrap: break-word; }
        .lokasi td { text-align: center; vertical-align: top; padding: 0; border-top: 1px solid #277a72; }
        .lokasi td + td { border-left: 1px solid #277a72; }
        .lokasi-name { background: #aeefd5; color: #277a72; font-size: 9pt; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; padding: 5px 4px; border-bottom: 1px solid #277a72; }
        .lokasi-val { font-weight: bold; padding: 16px 6px; word-wrap: break-word; }
        .foot { font-size: 7pt; color: #3e8f88; padding: 5px 14px; border-top: 1px solid #277a72; }
    </style>
</head>
<body>
    @foreach ($daftar as $item)
        @php
            $tahun = $item->tanggal_pengambilan?->format('Y') ?? '-';
            $lokasi = [
                'Nomor Ruang / Rak' => $item->nomor_ruang_arsip,
                'Nama Ruang' => $item->nama_ruang_arsip,
            ];
        @endphp
        <div @if (! $loop->last) style="page-break-after: always;" @endif>
        <table class="frame">
            <tr>
                <td style="padding: 0;">
                    <table class="head">
                        <tr>
                            <td style="width: 16%;">
                                @if ($logo)
                                    <img src="{{ $logo }}" class="logo" alt="Sucofindo">
                                @endif
                            </td>
                            <td style="width: 54%;">
                                <div class="brand">PT SUCOFINDO CILACAP</div>
                            </td>
                            <td style="width: 30%;">
                                <div class="sys">E-ARSIP SCI</div>
                                <div class="sys-sub">Label Sampel Arsip</div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr><td class="cap" style="border-top: 0;">Nama Sampel</td></tr>
            <tr><td class="judul">{{ $item->nama_sampel }}</td></tr>
            <tr>
                <td style="padding: 0;">
                    <table class="info">
                        <tr>
                            <td style="width: 60%;">
                                <div class="cap" style="border-top: 0;">Kode Sampel</div>
                                <div class="info-val">{{ $item->kode_sampel }}</div>
                            </td>
                            <td style="width: 40%;">
                                <div class="cap" style="border-top: 0;">Tahun</div>
                                <div class="info-val">{{ $tahun }}</div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr><td class="cap">Lokasi Penyimpanan</td></tr>
            <tr>
                <td style="padding: 0;">
                    <table class="lokasi">
                        <tr>
                            @foreach ($lokasi as $nama => $nilai)
                                <td style="width: 50%;">
                                    <div class="lokasi-name">{{ $nama }}</div>
                                    <div class="lokasi-val" style="font-size: {{ $ukuranNilai($nilai) }}pt;">{{ filled($nilai) ? $nilai : '-' }}</div>
                                </td>
                            @endforeach
                        </tr>
                    </table>
                </td>
            </tr>
            <tr><td class="foot">Dicetak dari E-Arsip SCI &middot; {{ now('Asia/Jakarta')->locale('id')->translatedFormat('d F Y') }}</td></tr>
        </table>
        </div>
    @endforeach
</body>
</html>