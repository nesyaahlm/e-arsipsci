<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DokumenSayaController extends Controller
{
    public function index(Request $request): View
    {
        $kata = trim((string) $request->query('q', ''));

        $dokumen = $this->kueri($request->user()->id)
            ->when($kata !== '', function (Builder $query) use ($kata) {
                $query->where(function (Builder $cari) use ($kata) {
                    $cari->where('dokumen.judul', 'like', '%'.$kata.'%')
                        ->orWhere('dokumen.nomor', 'like', '%'.$kata.'%');
                });
            })
            ->orderByDesc('dokumen.created_at')
            ->orderByDesc('dokumen.id')
            ->paginate(10)
            ->withQueryString()
            ->through(fn ($baris) => $this->susun($baris));

        return view('user.dokumen-saya.index', [
            'dokumen' => $dokumen,
            'kata' => $kata,
            ...app(UnggahDokumenController::class)->dataForm($request->user()),
        ]);
    }

    public function show(Request $request, int $dokumen): View
    {
        $baris = $this->kueri($request->user()->id)
            ->where('dokumen.id', $dokumen)
            ->first();

        abort_if($baris === null, 404);

        return view('user.dokumen-saya.show', [
            'dokumen' => $this->susun($baris),
        ]);
    }

    private function kueri(int $userId): Builder
    {
        return DB::table('dokumen')
            ->join('layanan', 'layanan.id', '=', 'dokumen.layanan_id')
            ->join('jenis_dokumen', 'jenis_dokumen.id', '=', 'dokumen.jenis_dokumen_id')
            ->whereNull('dokumen.deleted_at')
            ->where('dokumen.diunggah_oleh', $userId)
            ->select([
                'dokumen.id',
                'dokumen.nomor',
                'dokumen.judul',
                'dokumen.deskripsi',
                'dokumen.unit',
                'dokumen.tanggal_dokumen',
                'dokumen.kode_rak',
                'dokumen.nomor_rak',
                'dokumen.no_tingkat',
                'dokumen.no_box_file',
                'dokumen.lokasi_arsip_fisik',
                'dokumen.file_nama_asli',
                'dokumen.file_ukuran',
                'dokumen.retensi_sampai',
                'dokumen.file_path',
                'dokumen.created_at',
                'layanan.nama as portofolio',
                'jenis_dokumen.nama as jenis',
            ]);
    }

    private function susun(object $d): array
    {
        $tanggal = Carbon::parse($d->tanggal_dokumen)->locale('id');
        $diunggah = Carbon::parse($d->created_at)->locale('id');

        return [
            'id' => $d->id,
            'nomor' => $d->nomor,
            'judul' => $d->judul,
            'deskripsi' => $d->deskripsi,
            'jenis' => $d->jenis,
            'kode_rak' => $d->kode_rak,
            'nomor_rak' => $d->nomor_rak,
            'no_tingkat' => $d->no_tingkat,
            'no_box_file' => $d->no_box_file,
            'lokasi_terisi' => collect([$d->kode_rak, $d->nomor_rak, $d->no_tingkat, $d->no_box_file])
                ->contains(fn ($nilai) => filled($nilai)),
            'lokasi_lama' => $d->lokasi_arsip_fisik,
            'unit_kerja' => $d->unit,
            'portofolio' => $d->portofolio,
            'tahun' => $tanggal->year,
            'tanggal' => $tanggal->translatedFormat('d F Y'),
            'berkas' => $d->file_nama_asli,
            'ukuran' => $this->ukuran((int) $d->file_ukuran),
            'diunggah' => $diunggah->translatedFormat('d F Y'),
            'diunggah_singkat' => $diunggah->translatedFormat('d M Y'),
            'retensi' => $d->retensi_sampai
                ? Carbon::parse($d->retensi_sampai)->locale('id')->translatedFormat('d F Y')
                : null,
            'berkas_ada' => Storage::disk('public')->exists($d->file_path),
            'bisa_pratinjau' => in_array(strtolower(pathinfo($d->file_path, PATHINFO_EXTENSION)), ['pdf', 'png', 'jpg', 'jpeg'], true)
                && Storage::disk('public')->exists($d->file_path),
        ];
    }

    private function ukuran(int $byte): string
    {
        if ($byte >= 1048576) {
            return number_format($byte / 1048576, 1, ',', '.').' MB';
        }

        if ($byte >= 1024) {
            return number_format($byte / 1024, 0, ',', '.').' KB';
        }

        return $byte.' B';
    }
}