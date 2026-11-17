<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Bagian;
use App\Models\Cabang;
use App\Models\Sampel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SampelNasionalController extends Controller
{
    public function index(Request $request): View
    {
        $statusArsip = $request->query('status_arsip', 'semua');
        $cari = $request->query('cari');
        $cabangId = $request->query('cabang_id');
        $bagianId = $request->query('bagian_id');
        $tahun = $request->query('tahun');

        $baseCount = Sampel::when($cabangId, function ($q, $cabangId) {
            $q->where('cabang_id', $cabangId);
        });

        $totalSemua = (clone $baseCount)->count();
        $totalAktif = (clone $baseCount)->where('status_arsip', 'aktif')->count();
        $totalInaktif = (clone $baseCount)->where('status_arsip', 'inaktif')->count();
        $totalHapusBuku = (clone $baseCount)->where('status_arsip', 'hapus_buku')->count();

        $query = Sampel::with(['cabang', 'bagian', 'pengunggah'])
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
                $q->whereYear('tanggal_masuk_arsip', $tahun);
            })
            ->when($cari, function ($q, $cari) {
                $q->where(function ($sub) use ($cari) {
                    $sub->where('kode_sampel', 'like', "%{$cari}%")
                        ->orWhere('nama_sampel', 'like', "%{$cari}%")
                        ->orWhere('shipper', 'like', "%{$cari}%")
                        ->orWhere('vessel', 'like', "%{$cari}%")
                        ->orWhere('bill_of_lading', 'like', "%{$cari}%")
                        ->orWhere('nomor_buku', 'like', "%{$cari}%")
                        ->orWhere('nomor_ruang_arsip', 'like', "%{$cari}%")
                        ->orWhere('nama_ruang_arsip', 'like', "%{$cari}%");
                });
            })
            ->orderByDesc('tanggal_masuk_arsip')
            ->orderByDesc('id');

        $daftarSampel = $query->paginate(10)->withQueryString();
        $daftarCabang = Cabang::orderBy('nama', 'asc')->get();
        $daftarBagian = Bagian::orderBy('nama', 'asc')->get();
        $daftarTahun = Sampel::whereNotNull('tanggal_masuk_arsip')
            ->selectRaw('YEAR(tanggal_masuk_arsip) as tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        return view('superadmin.sampel.index', compact(
            'daftarSampel',
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

    public function foto(Sampel $sampel): BinaryFileResponse
    {
        if (! $sampel->foto_path || ! Storage::disk('public')->exists($sampel->foto_path)) {
            abort(404, 'Foto sampel tidak ditemukan.');
        }

        $path = Storage::disk('public')->path($sampel->foto_path);
        $mime = mime_content_type($path) ?: 'image/jpeg';

        return response()->file($path, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . basename($sampel->foto_path) . '"',
        ]);
    }
}