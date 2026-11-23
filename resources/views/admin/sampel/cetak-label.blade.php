<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Label - {{ $sampel->kode_sampel }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f8;
            font-family: Arial, Helvetica, sans-serif;
            color: #000000;
        }

        .label-container {
            width: 100%;
            max-width: 650px;
            margin: 20px auto;
            background: #ffffff;
            border: 2px solid #000000;
            border-radius: 8px;
            padding: 14px 18px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .label-header {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 2px solid #000000;
            padding-bottom: 10px;
            margin-bottom: 10px;
            min-height: 48px;
        }

        .label-logo {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            height: 38px;
            width: auto;
            object-fit: contain;
        }

        .label-header-title {
            font-size: 12.5pt;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin: 0;
            color: #000000;
            text-align: center;
            padding: 0 45px;
        }

        .table-label {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
        }

        .table-label td {
            padding: 6px 4px;
            vertical-align: middle;
            border-bottom: 1px solid #d1d5db;
        }

        .table-label tr:last-child td {
            border-bottom: none;
        }

        .field-title {
            width: 28%;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 9.5pt;
            color: #000000;
        }

        .field-colon {
            width: 3%;
            font-weight: 700;
            text-align: center;
        }

        .field-value {
            width: 69%;
            font-weight: 600;
            color: #000000;
            text-transform: uppercase;
        }

        .btn-cetak {
            background-color: #277a72;
            border-color: #277a72;
            color: #ffffff;
        }

        .btn-cetak:hover {
            background-color: #1e5f58;
            border-color: #1e5f58;
            color: #ffffff;
        }

        @media print {
            body {
                background: none;
                margin: 0;
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            .label-container {
                box-shadow: none;
                border: 2px solid #000000;
                border-radius: 6px;
                margin: 0;
                max-width: 100%;
                page-break-inside: avoid;
            }

            @page {
                size: auto;
                margin: 5mm;
            }
        }
    </style>
</head>
<body>
    @php
        $daftarKeterangan = collect([
            1 => $sampel->keterangan_1,
            2 => $sampel->keterangan_2,
            3 => $sampel->keterangan_3,
        ])
            ->map(fn ($k) => trim((string) $k))
            ->filter(fn ($k) => $k !== '' && $k !== '-');
    @endphp

    <div class="container my-3 no-print">
        <div class="d-flex justify-content-between align-items-center max-w-650 mx-auto" style="max-width: 650px;">
            <a href="{{ route('admin.sampel.show', $sampel->id) }}" class="btn btn-outline-secondary btn-sm px-3 fw-semibold">
                &larr; Kembali
            </a>
            <button onclick="window.print()" class="btn btn-cetak btn-sm px-4 fw-semibold shadow-sm">
                Cetak Label
            </button>
        </div>
    </div>

    <div class="label-container">
        <div class="label-header">
            <img src="{{ asset('assets/sucofindo-nobg.png') }}" alt="Logo Sucofindo" class="label-logo">
            <h1 class="label-header-title">PT. SUCOFINDO PERSERO</h1>
        </div>

        <table class="table-label">
            <tr>
                <td class="field-title">SHIPPER</td>
                <td class="field-colon">:</td>
                <td class="field-value">{{ $sampel->shipper ?: '-' }}</td>
            </tr>
            <tr>
                <td class="field-title">SAMPLE NAME</td>
                <td class="field-colon">:</td>
                <td class="field-value">{{ $sampel->nama_sampel }}</td>
            </tr>
            <tr>
                <td class="field-title">VESSEL</td>
                <td class="field-colon">:</td>
                <td class="field-value">{{ $sampel->vessel ?: '-' }}</td>
            </tr>
            <tr>
                <td class="field-title">TEMPAT PENGAMBILAN</td>
                <td class="field-colon">:</td>
                <td class="field-value">{{ $sampel->tempat_pengambilan ?: '-' }}</td>
            </tr>
            <tr>
                <td class="field-title">TANGGAL PENGAMBILAN</td>
                <td class="field-colon">:</td>
                <td class="field-value">{{ $sampel->tanggal_pengambilan?->format('d/m/Y') ?? '-' }}</td>
            </tr>
            @forelse ($daftarKeterangan as $nomor => $keterangan)
                <tr>
                    <td class="field-title">KETERANGAN {{ $nomor }}</td>
                    <td class="field-colon">:</td>
                    <td class="field-value">{{ $keterangan }}</td>
                </tr>
            @empty
                <tr>
                    <td class="field-title">KETERANGAN</td>
                    <td class="field-colon">:</td>
                    <td class="field-value">-</td>
                </tr>
            @endforelse
            <tr>
                <td class="field-title">SAMPEL ID</td>
                <td class="field-colon">:</td>
                <td class="field-value fw-bold font-monospace">{{ $sampel->kode_sampel }}</td>
            </tr>
        </table>
    </div>
</body>
</html>