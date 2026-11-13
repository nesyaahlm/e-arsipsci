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
    private const BATAS_TERBARU = 3;

    public function index(Request $request): View
    {
        $user = $request->user();

        return view('user.dashboard', [
            'user' => $user,
            'statistik' => $this->statistik($user->id),
            'arsipTerbaru' => $this->arsipTerbaru($user->id),
        ]);
    }

    private function dokumenSaya(int $userId): Builder
    {
        return DB::table('dokumen')
            ->whereNull('dokumen.deleted_at')
            ->where('dokumen.diunggah_oleh', $userId);
    }

    private function sampelSaya(int $userId): Builder
    {
        return DB::table('sampel')
            ->whereNull('sampel.deleted_at')
            ->where('sampel.diunggah_oleh', $userId);
    }

    private function statistik(int $userId): array
    {
        $dokumen = $this->dokumenSaya($userId)->count();
        $sampel = $this->sampelSaya($userId)->count();

        return [
            'total' => $dokumen + $sampel,
            'dokumen' => $dokumen,
            'sampel' => $sampel,
        ];
    }

    private function arsipTerbaru(int $userId): Collection
    {
        $dokumen = $this->dokumenSaya($userId)
            ->join('layanan', 'layanan.id', '=', 'dokumen.layanan_id')
            ->orderByDesc('dokumen.created_at')
            ->orderByDesc('dokumen.id')
            ->limit(self::BATAS_TERBARU)
            ->get([
                'dokumen.id',
                'dokumen.nomor',
                'dokumen.judul',
                'dokumen.created_at',
                'layanan.nama as layanan',
            ])
            ->map(fn ($d) => [
                'tipe' => 'dokumen',
                'id' => $d->id,
                'judul' => $d->judul,
                'keterangan' => $d->nomor.' · '.$this->tanggalUnggah($d->created_at),
                'layanan' => $d->layanan,
                'dibuat' => $d->created_at,
            ]);

        $sampel = $this->sampelSaya($userId)
            ->join('layanan', 'layanan.id', '=', 'sampel.layanan_id')
            ->orderByDesc('sampel.created_at')
            ->orderByDesc('sampel.id')
            ->limit(self::BATAS_TERBARU)
            ->get([
                'sampel.id',
                'sampel.kode_sampel',
                'sampel.nama_sampel',
                'sampel.created_at',
                'layanan.nama as layanan',
            ])
            ->map(fn ($s) => [
                'tipe' => 'sampel',
                'id' => $s->id,
                'judul' => $s->nama_sampel,
                'keterangan' => $s->kode_sampel.' · '.$this->tanggalUnggah($s->created_at),
                'layanan' => $s->layanan,
                'dibuat' => $s->created_at,
            ]);

        return $dokumen
            ->concat($sampel)
            ->sort(fn ($a, $b) => [$b['dibuat'], $b['tipe'], $b['id']] <=> [$a['dibuat'], $a['tipe'], $a['id']])
            ->take(self::BATAS_TERBARU)
            ->values();
    }

    private function tanggalUnggah(string $waktu): string
    {
        return Carbon::parse($waktu, config('app.timezone'))
            ->setTimezone('Asia/Jakarta')
            ->locale('id')
            ->translatedFormat('d M Y');
    }
}