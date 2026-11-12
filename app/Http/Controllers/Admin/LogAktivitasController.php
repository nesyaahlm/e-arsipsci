<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class LogAktivitasController extends Controller
{
    public function index(Request $request)
    {
        $cari = trim((string) $request->query('cari', ''));
        $aktivitas = $request->query('aktivitas');
        $tanggal = $request->query('tanggal');

        $logs = LogAktivitas::with(['user', 'dokumen'])
            ->when($cari !== '', function ($query) use ($cari) {
                $query->where(function ($q) use ($cari) {
                    $q->where('keterangan', 'like', "%{$cari}%")
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$cari}%"))
                        ->orWhereHas('dokumen', function ($d) use ($cari) {
                            $d->where(function ($x) use ($cari) {
                                $x->where('nomor', 'like', "%{$cari}%")
                                    ->orWhere('judul', 'like', "%{$cari}%");
                            });
                        });
                });
            })
            ->when($aktivitas, fn ($query) => $query->where('aktivitas', $aktivitas))
            ->when($tanggal && strtotime($tanggal), fn ($query) => $query->whereDate('created_at', $tanggal))
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $daftarAktivitas = LogAktivitas::query()
            ->select('aktivitas')
            ->distinct()
            ->orderBy('aktivitas')
            ->pluck('aktivitas');

        return view('admin.log-aktivitas', compact('logs', 'daftarAktivitas', 'cari', 'aktivitas', 'tanggal'));
    }
}