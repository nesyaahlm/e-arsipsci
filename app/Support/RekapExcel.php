<?php

namespace App\Support;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RekapExcel
{
    public static function unduh(string $namaFile, string $namaSheet, string $judul, string $keterangan, array $kolom, iterable $baris): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($namaSheet);

        $kolomAkhir = Coordinate::stringFromColumnIndex(count($kolom) + 1);
        $barisHeader = 4;

        $sheet->setCellValue('A1', $judul);
        $sheet->mergeCells("A1:{$kolomAkhir}1");
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        $sheet->setCellValue('A2', $keterangan);
        $sheet->mergeCells("A2:{$kolomAkhir}2");
        $sheet->getStyle('A2')->getFont()->setItalic(true);

        $sheet->setCellValue('A' . $barisHeader, 'No');
        $sheet->getColumnDimension('A')->setWidth(6);

        foreach (array_values($kolom) as $indeks => $definisi) {
            $huruf = Coordinate::stringFromColumnIndex($indeks + 2);
            $sheet->setCellValue($huruf . $barisHeader, $definisi['judul']);
            $sheet->getColumnDimension($huruf)->setWidth($definisi['lebar']);
        }

        $nomor = 0;

        foreach ($baris as $isi) {
            $nomor++;
            $r = $barisHeader + $nomor;

            $sheet->setCellValue('A' . $r, $nomor);

            foreach (array_values($isi) as $indeks => $nilai) {
                $huruf = Coordinate::stringFromColumnIndex($indeks + 2);
                $sheet->setCellValueExplicit($huruf . $r, (string) ($nilai ?? ''), DataType::TYPE_STRING);
            }
        }

        $barisAkhir = $barisHeader + $nomor;

        $sheet->getStyle("A{$barisHeader}:{$kolomAkhir}{$barisHeader}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '1F5F55']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E3F1EE'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);

        $sheet->getStyle("A{$barisHeader}:{$kolomAkhir}{$barisAkhir}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CFDEDB'],
                ],
            ],
        ]);

        if ($nomor > 0) {
            $awalData = $barisHeader + 1;

            $sheet->getStyle("A{$awalData}:{$kolomAkhir}{$barisAkhir}")
                ->getAlignment()
                ->setVertical(Alignment::VERTICAL_TOP)
                ->setWrapText(true);

            $sheet->getStyle("A{$awalData}:A{$barisAkhir}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        $sheet->freezePane('A' . ($barisHeader + 1));
        $sheet->setAutoFilter("A{$barisHeader}:{$kolomAkhir}{$barisAkhir}");

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
            $spreadsheet->disconnectWorksheets();
        }, $namaFile, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}