<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Cabang;
use App\Models\LogAktivitas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogAktivitasController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->query('tab', 'aktif');
        $cari = trim((string) $request->query('cari', ''));
        $cabangId = $request->query('cabang_id');
        $aktivitas = $request->query('aktivitas');
        $tanggal = $request->query('tanggal');

        $totalAktif = LogAktivitas::aktif()->count();
        $totalArsip = LogAktivitas::diarsipkan()->count();

        $query = LogAktivitas::with(['user.cabang', 'dokumen'])
            ->when($tab === 'arsip', function ($q) {
                $q->diarsipkan();
            }, function ($q) {
                $q->aktif();
            })
            ->when($cari !== '', function ($q) use ($cari) {
                $q->where(function ($sub) use ($cari) {
                    $sub->where('keterangan', 'like', "%{$cari}%")
                        ->orWhere('aktivitas', 'like', "%{$cari}%")
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$cari}%"))
                        ->orWhereHas('dokumen', function ($d) use ($cari) {
                            $d->where(function ($x) use ($cari) {
                                $x->where('nomor', 'like', "%{$cari}%")
                                    ->orWhere('judul', 'like', "%{$cari}%");
                            });
                        });
                });
            })
            ->when($cabangId, function ($q, $cabangId) {
                $q->whereHas('user', fn ($u) => $u->where('cabang_id', $cabangId));
            })
            ->when($aktivitas, function ($q, $aktivitas) {
                $q->where('aktivitas', $aktivitas);
            })
            ->when($tanggal, function ($q, $tanggal) {
                $q->whereDate('created_at', $tanggal);
            })
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        $logs = $query->paginate(15)->withQueryString();

        $daftarCabang = Cabang::orderBy('nama', 'asc')->get();

        $daftarAktivitas = LogAktivitas::select('aktivitas')
            ->distinct()
            ->orderBy('aktivitas')
            ->pluck('aktivitas');

        return view('superadmin.log-aktivitas.index', compact(
            'logs',
            'daftarCabang',
            'daftarAktivitas',
            'cari',
            'cabangId',
            'aktivitas',
            'tanggal',
            'tab',
            'totalAktif',
            'totalArsip'
        ));
    }

    public function arsipkan(Request $request): RedirectResponse
    {
        $hari = (int) $request->input('hari', 30);
        $jumlah = LogAktivitas::arsipkanLama($hari);

        return redirect()
            ->route('superadmin.log-aktivitas.index', ['tab' => 'arsip'])
            ->with('sukses', "Sebanyak {$jumlah} log aktivitas berhasil dipindahkan ke Arsip Log.");
    }

    public function hapusArsip(Request $request): RedirectResponse
    {
        $ids = $request->input('ids');
        $jumlah = LogAktivitas::hapusArsip($ids);

        return redirect()
            ->route('superadmin.log-aktivitas.index', ['tab' => 'arsip'])
            ->with('sukses', "Sebanyak {$jumlah} log arsip berhasil dibersihkan permanen.");
    }
}