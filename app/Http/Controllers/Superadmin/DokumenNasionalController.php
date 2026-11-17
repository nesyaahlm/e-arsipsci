<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Bagian;
use App\Models\Cabang;
use App\Models\Dokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DokumenNasionalController extends Controller
{
    public function index(Request $request): View
    {
        $statusArsip = $request->query('status_arsip', 'semua');
        $cari = $request->query('cari');
        $cabangId = $request->query('cabang_id');
        $bagianId = $request->query('bagian_id');
        $tahun = $request->query('tahun');

        $baseCount = Dokumen::when($cabangId, function ($q, $cabangId) {
            $q->where('cabang_id', $cabangId);
        });

        $totalSemua = (clone $baseCount)->count();
        $totalAktif = (clone $baseCount)->where('status_arsip', 'aktif')->count();
        $totalInaktif = (clone $baseCount)->where('status_arsip', 'inaktif')->count();
        $totalHapusBuku = (clone $baseCount)->where('status_arsip', 'hapus_buku')->count();

        $query = Dokumen::with(['cabang', 'bagian', 'pengunggah'])
            ->when($statusArsip !== 'semua', function ($q) use ($statusArsip) {
                $q->where('status_arsip', $statusArsip);
            })
            ->when($cabangId, function ($q, $cabangId) {
                $q->where('cabang_id', $cabangId);
            })
            ->when($bagianId, function ($q, $bagianId) {
                $q->where('bagian_id', $bagianId);
            })
            ->when($tahun, function ($q, $tahun) {
                $q->whereYear('tanggal_dokumen', $tahun);
            })
            ->when($cari, function ($q, $cari) {
                $q->where(function ($sub) use ($cari) {
                    $sub->where('nomor', 'like', "%{$cari}%")
                        ->orWhere('judul', 'like', "%{$cari}%")
                        ->orWhere('nomor_buku', 'like', "%{$cari}%")
                        ->orWhere('unit', 'like', "%{$cari}%");
                });
            })
            ->latest('tanggal_dokumen');

        $daftarDokumen = $query->paginate(10)->withQueryString();
        $daftarCabang = Cabang::orderBy('nama', 'asc')->get();
        $daftarBagian = Bagian::orderBy('nama', 'asc')->get();
        $daftarTahun = Dokumen::whereNotNull('tanggal_dokumen')
            ->selectRaw('YEAR(tanggal_dokumen) as tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        return view('superadmin.dokumen.index', compact(
            'daftarDokumen',
            'daftarCabang',
            'daftarBagian',
            'daftarTahun',
            'statusArsip',
            'cari',
            'cabangId',
            'bagianId',
            'tahun',
            'totalSemua',
            'totalAktif',
            'totalInaktif',
            'totalHapusBuku'
        ));
    }

    public function unduh(Dokumen $dokumen)
    {
        if (! $dokumen->file_path || ! Storage::disk('public')->exists($dokumen->file_path)) {
            return back()->with('galat', 'Berkas fisik dokumen tidak ditemukan di server penyimpanan.');
        }

        return Storage::disk('public')->download(
            $dokumen->file_path,
            $dokumen->file_nama_asli ?: basename($dokumen->file_path)
        );
    }

    public function preview(Dokumen $dokumen)
    {
        if (! $dokumen->file_path || ! Storage::disk('public')->exists($dokumen->file_path)) {
            abort(404, 'Berkas fisik dokumen tidak ditemukan.');
        }

        $path = Storage::disk('public')->path($dokumen->file_path);
        $mime = mime_content_type($path) ?: 'application/octet-stream';

        return response()->file($path, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . ($dokumen->file_nama_asli ?: basename($dokumen->file_path)) . '"',
        ]);
    }
}