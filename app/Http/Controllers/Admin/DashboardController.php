<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dokumen;
use App\Models\Sampel;
use App\Models\JenisDokumen;
use App\Models\Layanan;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $hariIni = today();

        return view('admin.dashboard', [
            'totalDokumen' => Dokumen::count(),
            'totalSampel' => Sampel::count(),
            'retensiDekat' => Dokumen::whereBetween('retensi_sampai', [$hariIni, $hariIni->copy()->addDays(60)])->count(),
            'totalJenis' => JenisDokumen::count(),
            'totalLayanan' => Layanan::count(),
            'daftarLayanan' => Layanan::withCount('dokumen')->orderBy('id')->get(),
            
        ]);
    }
}