<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('user.dashboard', [
            'user' => $user,
            'statistik' => $this->statistik($user->id),
            'dokumenTerbaru' => $this->dokumenTerbaru(),
            'aktivitas' => $this->aktivitas($user->id),
        ]);
    }

    private function dokumenAktif(): Builder
    {
        return DB::table('dokumen')->whereNull('dokumen.deleted_at');
    }

    private function dokumenArsip(): Builder
    {
        return $this->dokumenAktif()->where('dokumen.status', 'disetujui');
    }

    private function statistik(int $userId): array
    {
        $milikSaya = $this->dokumenAktif()->where('dokumen.diunggah_oleh', $userId);

        return [
            'saya' => $milikSaya->clone()->count(),
            'bulan_ini' => $milikSaya->clone()->where('dokumen.created_at', '>=', now()->startOfMonth())->count(),
            'favorit' => DB::table('favorit')
                ->join('dokumen', 'dokumen.id', '=', 'favorit.dokumen_id')
                ->whereNull('dokumen.deleted_at')
                ->where('dokumen.status', 'disetujui')
                ->where('favorit.user_id', $userId)
                ->count(),
            'total' => $this->dokumenArsip()->count(),
            'bulan' => now()->locale('id')->translatedFormat('F Y'),
        ];
    }

    private function dokumenTerbaru(): Collection
    {
        return $this->dokumenArsip()
            ->join('layanan', 'layanan.id', '=', 'dokumen.layanan_id')
            ->orderByDesc('dokumen.tanggal_dokumen')
            ->orderByDesc('dokumen.id')
            ->limit(3)
            ->get([
                'dokumen.id',
                'dokumen.nomor',
                'dokumen.judul',
                'dokumen.tanggal_dokumen',
                'layanan.nama as layanan',
            ])
            ->map(fn ($d) => [
                'id' => $d->id,
                'judul' => $d->judul,
                'keterangan' => $d->nomor.' · '.$this->tanggal($d->tanggal_dokumen),
                'layanan' => $d->layanan,
            ]);
    }

    private function aktivitas(int $userId): Collection
    {
        $favorit = DB::table('favorit')
            ->join('dokumen', 'dokumen.id', '=', 'favorit.dokumen_id')
            ->join('layanan', 'layanan.id', '=', 'dokumen.layanan_id')
            ->whereNull('dokumen.deleted_at')
            ->where('dokumen.status', 'disetujui')
            ->where('favorit.user_id', $userId)
            ->orderByDesc('dokumen.tanggal_dokumen')
            ->limit(3)
            ->get(['dokumen.id', 'dokumen.judul', 'layanan.nama as layanan'])
            ->map(fn ($d) => [
                'id' => $d->id,
                'judul' => $d->judul,
                'keterangan' => 'Favorit - '.$d->layanan,
                'ikon' => 'star',
            ]);

        $dibuka = DB::table('riwayat_dibuka')
            ->join('dokumen', 'dokumen.id', '=', 'riwayat_dibuka.dokumen_id')
            ->join('layanan', 'layanan.id', '=', 'dokumen.layanan_id')
            ->whereNull('dokumen.deleted_at')
            ->where('dokumen.status', 'disetujui')
            ->where('riwayat_dibuka.user_id', $userId)
            ->orderByDesc('riwayat_dibuka.dibuka_pada')
            ->limit(3)
            ->get(['dokumen.id', 'dokumen.judul', 'layanan.nama as layanan', 'riwayat_dibuka.dibuka_pada'])
            ->map(fn ($d) => [
                'id' => $d->id,
                'judul' => $d->judul,
                'keterangan' => 'Dibuka '.Carbon::parse($d->dibuka_pada)->locale('id')->diffForHumans().' - '.$d->layanan,
                'ikon' => 'clock',
            ]);

        return $favorit->concat($dibuka)->values();
    }

    private function tanggal(string $tanggal): string
    {
        return Carbon::parse($tanggal)->locale('id')->translatedFormat('d M Y');
    }
}