<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class LogArsipController extends Controller
{
    public function index(Request $request)
    {
        LogAktivitas::arsipkanLama();

        $cari = trim((string) $request->query('cari', ''));
        $aktivitas = $request->query('aktivitas');
        $tanggal = $request->query('tanggal');

        $logs = LogAktivitas::with(['user', 'dokumen'])
            ->diarsipkan()
            ->filterLog($cari, $aktivitas, $tanggal)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $daftarAktivitas = LogAktivitas::diarsipkan()
            ->select('aktivitas')
            ->distinct()
            ->orderBy('aktivitas')
            ->pluck('aktivitas');

        $totalArsip = LogAktivitas::diarsipkan()->count();

        return view('admin.log-arsip', compact('logs', 'daftarAktivitas', 'cari', 'aktivitas', 'tanggal', 'totalArsip'));
    }

    public function hapusTerpilih(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer',
        ], [
            'ids.required' => 'Pilih minimal satu log yang ingin dihapus.',
            'ids.min' => 'Pilih minimal satu log yang ingin dihapus.',
        ]);

        $jumlah = LogAktivitas::hapusArsip($request->input('ids'));

        LogAktivitas::catat('menghapus_arsip_log', null, "Menghapus {$jumlah} log arsip terpilih");

        return redirect()->route('log.arsip.index')->with('sukses', "{$jumlah} log arsip berhasil dihapus.");
    }

    public function hapusSemua()
    {
        $jumlah = LogAktivitas::hapusArsip();

        LogAktivitas::catat('menghapus_arsip_log', null, "Menghapus semua arsip ({$jumlah} log)");

        return redirect()->route('log.arsip.index')->with('sukses', "{$jumlah} log arsip berhasil dihapus.");
    }
}