<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Bagian;
use App\Models\Dokumen;
use App\Models\KodeSurat;
use App\Models\Sampel;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('superadmin.dashboard', [
            'totalDokumen' => Dokumen::count(),
            'totalSampel' => Sampel::count(),
            'totalBagian' => Bagian::count(),
            'totalKodeSurat' => KodeSurat::count(),
            'totalAdminCabang' => User::where('role', 'admin')->count(),
            'daftarBagian' => Bagian::withCount('dokumen')->orderBy('nama')->get(),
            'dokumenTerbaru' => Dokumen::with('bagian')->latest()->take(5)->get(),
            'sampelTerbaru' => Sampel::with('bagian')->latest()->take(5)->get(),
        ]);
    }
}