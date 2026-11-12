<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class ArsipController extends Controller
{
    private const MIME_PRATINJAU = [
        'pdf' => 'application/pdf',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
    ];

    private const KOLOM_CARI = [
        'dokumen.judul' => 5,
        'dokumen.nomor' => 4,
        'dokumen.deskripsi' => 2,
        'dokumen.unit' => 2,
        'layanan.nama' => 2,
        'jenis_dokumen.nama' => 1,
        'dokumen.kode_rak' => 1,
        'dokumen.nomor_rak' => 1,
        'dokumen.no_tingkat' => 1,
        'dokumen.no_box_file' => 1,
        'dokumen.lokasi_arsip_fisik' => 1,
    ];

    public function index(Request $request): View
    {
        $kata = Str::limit(trim((string) $request->query('q', '')), 150, '');
        $terms = array_slice(preg_split('/\s+/u', $kata, -1, PREG_SPLIT_NO_EMPTY) ?: [], 0, 6);

        $layananId = $request->integer('layanan_id') ?: null;
        $jenisId = $request->integer('jenis_dokumen_id') ?: null;
        $tahun = $request->integer('tahun') ?: null;
        $unit = Str::limit(trim((string) $request->query('unit', '')), 100, '') ?: null;
        $urut = $request->query('urut') === 'terbaru' || $terms === [] ? 'terbaru' : 'relevansi';

        $kueri = $this->arsipTersedia()
            ->when($layananId, fn (Builder $q) => $q->where('dokumen.layanan_id', $layananId))
            ->when($jenisId, fn (Builder $q) => $q->where('dokumen.jenis_dokumen_id', $jenisId))
            ->when($tahun, fn (Builder $q) => $q->whereYear('dokumen.tanggal_dokumen', $tahun))
            ->when($unit, fn (Builder $q) => $q->where('dokumen.unit', $unit));

        foreach ($terms as $term) {
            $like = '%'.$this->escapeLike($term).'%';

            $kueri->where(function (Builder $q) use ($like) {
                foreach (array_keys(self::KOLOM_CARI) as $kolom) {
                    $q->orWhereRaw($kolom." LIKE ? ESCAPE '!'", [$like]);
                }
            });
        }

        if ($urut === 'relevansi') {
            $skor = [];
            $binding = [];

            foreach ($terms as $term) {
                $like = '%'.$this->escapeLike($term).'%';

                foreach (self::KOLOM_CARI as $kolom => $bobot) {
                    $skor[] = 'CASE WHEN '.$kolom." LIKE ? ESCAPE '!' THEN ".$bobot.' ELSE 0 END';
                    $binding[] = $like;
                }
            }

            $kueri->orderByRaw('('.implode(' + ', $skor).') DESC', $binding);
        }

        $hasil = $kueri
            ->orderByDesc('dokumen.tanggal_dokumen')
            ->orderByDesc('dokumen.id')
            ->select($this->kolomDaftar())
            ->paginate(10)
            ->withQueryString()
            ->through(fn ($baris) => $this->susunDaftar($baris));

        return view('user.arsip.index', [
            'hasil' => $hasil,
            'kata' => $kata,
            'filter' => [
                'layanan_id' => $layananId,
                'jenis_dokumen_id' => $jenisId,
                'tahun' => $tahun,
                'unit' => $unit,
                'urut' => $urut,
            ],
            'layananList' => DB::table('layanan')->orderBy('nama')->get(['id', 'nama']),
            'jenisList' => DB::table('jenis_dokumen')->orderBy('nama')->get(['id', 'nama']),
            'tahunList' => $this->tahunTersedia(),
            'unitList' => $this->unitTersedia(),
            'adaFilter' => $layananId || $jenisId || $tahun || $unit,
        ]);
    }

    public function show(Request $request, int $dokumen): View
    {
        $baris = $this->arsipTersedia()
            ->where('dokumen.id', $dokumen)
            ->first($this->kolomDetail());

        abort_if($baris === null, 404);

        try {
            DB::table('riwayat_dibuka')->upsert(
                [['user_id' => $request->user()->id, 'dokumen_id' => $baris->id, 'dibuka_pada' => now()]],
                ['user_id', 'dokumen_id'],
                ['dibuka_pada']
            );
        } catch (Throwable $e) {
            report($e);
        }

        return view('user.arsip.show', [
            'arsip' => $this->susunDetail($baris),
            'kembali' => $this->urlKembali(),
        ]);
    }

    public function pratinjau(int $dokumen): BinaryFileResponse
    {
        $baris = $this->arsipTersedia()
            ->where('dokumen.id', $dokumen)
            ->first(['dokumen.file_path', 'dokumen.file_nama_asli']);

        abort_if($baris === null, 404);

        $ekstensi = strtolower(pathinfo($baris->file_path, PATHINFO_EXTENSION));

        abort_unless(isset(self::MIME_PRATINJAU[$ekstensi]), 404);
        abort_unless(Storage::disk('public')->exists($baris->file_path), 404);

        $nama = $this->namaBerkas($baris);

        $respons = response()->file(Storage::disk('public')->path($baris->file_path), [
            'Content-Type' => self::MIME_PRATINJAU[$ekstensi],
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-cache, must-revalidate',
        ]);

        $respons->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            $nama,
            str_replace(['%', '/', '\\'], '-', Str::ascii($nama))
        );

        return $respons;
    }

    public function unduh(Request $request, int $dokumen): StreamedResponse
    {
        $baris = $this->arsipTersedia()
            ->where('dokumen.id', $dokumen)
            ->first(['dokumen.id', 'dokumen.file_path', 'dokumen.file_nama_asli']);

        abort_if($baris === null, 404);
        abort_unless(Storage::disk('public')->exists($baris->file_path), 404);

        try {
            DB::table('log_aktivitas')->insert([
                'user_id' => $request->user()->id,
                'aktivitas' => 'mengunduh',
                'dokumen_id' => $baris->id,
                'keterangan' => null,
                'created_at' => now(),
            ]);
        } catch (Throwable $e) {
            report($e);
        }

        return Storage::disk('public')->download($baris->file_path, $this->namaBerkas($baris));
    }

    private function arsipTersedia(): Builder
    {
        return DB::table('dokumen')
            ->join('layanan', 'layanan.id', '=', 'dokumen.layanan_id')
            ->join('jenis_dokumen', 'jenis_dokumen.id', '=', 'dokumen.jenis_dokumen_id')
            ->whereNull('dokumen.deleted_at')
            ->where('dokumen.status', 'disetujui');
    }

    private function kolomDaftar(): array
    {
        return [
            'dokumen.id',
            'dokumen.nomor',
            'dokumen.judul',
            'dokumen.unit',
            'dokumen.tanggal_dokumen',
            'dokumen.kode_rak',
            'dokumen.nomor_rak',
            'dokumen.no_tingkat',
            'dokumen.no_box_file',
            'dokumen.lokasi_arsip_fisik',
            'dokumen.created_at',
            'layanan.nama as layanan',
            'jenis_dokumen.nama as jenis',
        ];
    }

    private function kolomDetail(): array
    {
        return array_merge($this->kolomDaftar(), [
            'dokumen.deskripsi',
            'dokumen.file_path',
            'dokumen.file_nama_asli',
            'dokumen.file_ukuran',
            'dokumen.retensi_sampai',
        ]);
    }

    private function petaLokasi(object $d): array
    {
        $lokasi = (string) ($d->lokasi_arsip_fisik ?? '');

        $ambil = function (string $pola) use ($lokasi): ?string {
            if (preg_match($pola, $lokasi, $cocok)) {
                $nilai = trim($cocok[1]);

                return $nilai !== '' ? $nilai : null;
            }

            return null;
        };

        $kolom = [
            'kode_rak' => $d->kode_rak,
            'nomor_rak' => $d->nomor_rak,
            'no_tingkat' => $d->no_tingkat,
            'no_box_file' => $d->no_box_file,
        ];

        $cadangan = [
            'kode_rak' => null,
            'nomor_rak' => $ambil('/Rak\s*([^\/]+)/i'),
            'no_tingkat' => $ambil('/Tingkat\s*([^\/]+)/i'),
            'no_box_file' => $ambil('/Box\s*([^\/]+)/i'),
        ];

        $hasil = [];

        foreach ($kolom as $nama => $nilai) {
            $hasil[$nama] = filled($nilai) ? $nilai : $cadangan[$nama];
        }

        return $hasil;
    }

    private function susunDaftar(object $d): array
    {
        $lokasi = $this->petaLokasi($d);

        $slot = [
            'Kode Rak' => $lokasi['kode_rak'],
            'No. Rak' => $lokasi['nomor_rak'],
            'Tingkat' => $lokasi['no_tingkat'],
            'Box' => $lokasi['no_box_file'],
        ];

        return [
            'id' => $d->id,
            'nomor' => $d->nomor,
            'judul' => $d->judul,
            'jenis' => $d->jenis,
            'layanan' => $d->layanan,
            'unit' => $d->unit,
            'tahun' => Carbon::parse($d->tanggal_dokumen)->year,
            'diunggah' => $this->waktuLokal($d->created_at)->translatedFormat('d M Y'),
            'slot' => $slot,
            'slot_terisi' => collect($slot)->contains(fn ($nilai) => filled($nilai)),
            'lokasi_lama' => $d->lokasi_arsip_fisik,
        ];
    }

    private function susunDetail(object $d): array
    {
        $tanggal = Carbon::parse($d->tanggal_dokumen)->locale('id');
        $ekstensi = strtolower(pathinfo($d->file_path, PATHINFO_EXTENSION));
        $ada = Storage::disk('public')->exists($d->file_path);
        $lokasi = $this->petaLokasi($d);

        return [
            'id' => $d->id,
            'nomor' => $d->nomor,
            'judul' => $d->judul,
            'deskripsi' => $d->deskripsi,
            'jenis' => $d->jenis,
            'layanan' => $d->layanan,
            'kode_rak' => $lokasi['kode_rak'],
            'nomor_rak' => $lokasi['nomor_rak'],
            'no_tingkat' => $lokasi['no_tingkat'],
            'no_box_file' => $lokasi['no_box_file'],
            'lokasi_terisi' => collect($lokasi)->contains(fn ($nilai) => filled($nilai)),
            'lokasi_lama' => $d->lokasi_arsip_fisik,
            'unit_kerja' => $d->unit,
            'portofolio' => $d->layanan,
            'tahun' => $tanggal->year,
            'tanggal' => $tanggal->translatedFormat('d F Y'),
            'diunggah' => $this->waktuLokal($d->created_at)->translatedFormat('d F Y'),
            'retensi' => $d->retensi_sampai
                ? Carbon::parse($d->retensi_sampai)->locale('id')->translatedFormat('d F Y')
                : null,
            'berkas' => $this->namaBerkas($d),
            'tipe' => strtoupper($ekstensi) ?: '-',
            'ukuran' => $this->ukuran((int) $d->file_ukuran),
            'berkas_ada' => $ada,
            'bisa_pratinjau' => $ada && isset(self::MIME_PRATINJAU[$ekstensi]),
            'pratinjau_gambar' => $ekstensi !== 'pdf',
        ];
    }

    private function tahunTersedia(): Collection
    {
        return $this->arsipTersedia()
            ->distinct()
            ->pluck('dokumen.tanggal_dokumen')
            ->map(fn ($tanggal) => Carbon::parse($tanggal)->year)
            ->unique()
            ->sortDesc()
            ->values();
    }

    private function unitTersedia(): Collection
    {
        return $this->arsipTersedia()
            ->whereNotNull('dokumen.unit')
            ->where('dokumen.unit', '!=', '')
            ->distinct()
            ->orderBy('dokumen.unit')
            ->pluck('dokumen.unit');
    }

    private function urlKembali(): string
    {
        $indeks = route('user.arsip.index');
        $sebelumnya = url()->previous();

        if (
            parse_url($sebelumnya, PHP_URL_PATH) === parse_url($indeks, PHP_URL_PATH)
            && parse_url($sebelumnya, PHP_URL_HOST) === parse_url($indeks, PHP_URL_HOST)
        ) {
            return $sebelumnya;
        }

        return $indeks;
    }

    private function namaBerkas(object $d): string
    {
        $nama = trim((string) ($d->file_nama_asli ?? ''));

        return $nama !== '' ? $nama : basename($d->file_path);
    }

    private function waktuLokal(?string $waktu): Carbon
    {
        return Carbon::parse($waktu, config('app.timezone'))
            ->setTimezone('Asia/Jakarta')
            ->locale('id');
    }

    private function escapeLike(string $nilai): string
    {
        return str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $nilai);
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