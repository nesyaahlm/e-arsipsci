<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class LogAktivitasController extends Controller
{
    public function index(Request $request)
    {
        LogAktivitas::arsipkanLama();

        $cari = trim((string) $request->query('cari', ''));
        $aktivitas = $request->query('aktivitas');
        $tanggal = $request->query('tanggal');

        $logs = LogAktivitas::with(['user', 'dokumen'])
            ->aktif()
            ->filterLog($cari, $aktivitas, $tanggal)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $daftarAktivitas = LogAktivitas::aktif()
            ->select('aktivitas')
            ->distinct()
            ->orderBy('aktivitas')
            ->pluck('aktivitas');

        $totalArsip = LogAktivitas::diarsipkan()->count();

        return view('admin.log-aktivitas', compact('logs', 'daftarAktivitas', 'cari', 'aktivitas', 'tanggal', 'totalArsip'));
    }
}