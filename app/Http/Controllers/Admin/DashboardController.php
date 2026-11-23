<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bagian;
use App\Models\Cabang;
use App\Models\Dokumen;
use App\Models\JenisDokumen;
use App\Models\Portofolio;
use App\Models\Sampel;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $hariIni = today();
        $userCabangId = Auth::user()->cabang_id ?? Cabang::where('kode', 'CLC')->value('id') ?? 1;

        $daftarBagian = Bagian::withCount('dokumen')
            ->orderByDesc('dokumen_count')
            ->orderBy('nama')
            ->get();

        $daftarPortofolio = Portofolio::withCount('sampel')
            ->where(function ($q) use ($userCabangId) {
                $q->whereNull('cabang_id')
                    ->orWhere('cabang_id', $userCabangId);
            })
            ->orderByDesc('sampel_count')
            ->orderBy('nama')
            ->get();

        return view('admin.dashboard', [
            'totalDokumen' => Dokumen::count(),
            'totalSampel' => Sampel::count(),
            'retensiDekat' => Dokumen::whereBetween('retensi_sampai', [$hariIni, $hariIni->copy()->addDays(60)])->count(),
            'totalJenis' => JenisDokumen::count(),
            'totalBagian' => Bagian::count(),
            'daftarBagian' => $daftarBagian,
            'daftarPortofolio' => $daftarPortofolio,
            'maksDokumen' => max(1, (int) $daftarBagian->max('dokumen_count')),
            'maksSampel' => max(1, (int) $daftarPortofolio->max('sampel_count')),
        ]);
    }
}