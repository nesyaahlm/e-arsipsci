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

    private const KOLOM_CARI_SAMPEL = [
        'sampel.nama_sampel' => 5,
        'sampel.kode_sampel' => 4,
        'sampel.analysis_for' => 2,
        'sampel.deskripsi' => 2,
        'layanan.nama' => 2,
        'sampel.shipper' => 1,
        'sampel.vessel' => 1,
        'sampel.bill_of_lading' => 1,
        'sampel.tempat_pengambilan' => 1,
        'sampel.nama_ruang_arsip' => 1,
        'sampel.nomor_ruang_arsip' => 1,
    ];

    public function index(Request $request): View
    {
        $kata = Str::limit(trim((string) $request->query('q', '')), 150, '');
        $terms = array_slice(preg_split('/\s+/u', $kata, -1, PREG_SPLIT_NO_EMPTY) ?: [], 0, 6);

        $kategori = in_array($request->query('kategori'), ['semua', 'dokumen', 'sampel'], true)
            ? $request->query('kategori')
            : 'semua';

        $userId = $request->user()->id;

        $tahunList = match ($kategori) {
            'dokumen' => $this->tahunTersedia(),
            'sampel' => $this->tahunSampel($userId),
            default => $this->tahunTersedia()->merge($this->tahunSampel($userId))->unique()->sortDesc()->values(),
        };

        $layananId = $request->integer('layanan_id') ?: null;
        $tahun = $request->integer('tahun') ?: null;

        if ($tahun !== null && ! $tahunList->contains($tahun)) {
            $tahun = null;
        }

        $urut = in_array($request->query('urut'), ['relevansi', 'terbaru', 'terlama'], true)
            ? $request->query('urut')
            : 'relevansi';

        if ($urut === 'relevansi' && $terms === []) {
            $urut = 'terbaru';
        }

        if ($kategori === 'sampel') {
            $hasil = $this->cariSampel($userId, $terms, $layananId, $tahun, $urut);
        } elseif ($kategori === 'dokumen') {
            $hasil = $this->cariDokumen($terms, $layananId, $tahun, $urut);
        } else {
            $hasil = $this->cariGabungan($userId, $terms, $layananId, $tahun, $urut);
        }

        return view('user.arsip.index', [
            'hasil' => $hasil,
            'kata' => $kata,
            'kategori' => $kategori,
            'filter' => [
                'layanan_id' => $layananId,
                'tahun' => $tahun,
                'urut' => $urut,
            ],
            'layananList' => DB::table('layanan')->orderBy('nama')->get(['id', 'nama']),
            'tahunList' => $tahunList,
            'adaFilter' => (bool) ($layananId || $tahun),
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

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        return $disk->download($baris->file_path, $this->namaBerkas($baris));
    }

    private function cariDokumen(array $terms, ?int $layananId, ?int $tahun, string $urut)
    {
        $arah = $urut === 'terlama' ? 'asc' : 'desc';
        $kueri = $this->kueriDokumen($terms, $layananId, $tahun);

        if ($urut === 'relevansi') {
            [$skor, $binding] = $this->ekspresiSkor(self::KOLOM_CARI, $terms);

            $kueri->orderByRaw($skor.' DESC', $binding);
        }

        return $kueri
            ->orderBy('dokumen.tanggal_dokumen', $arah)
            ->orderBy('dokumen.id', $arah)
            ->select($this->kolomDaftar())
            ->paginate(10)
            ->withQueryString()
            ->through(fn ($baris) => $this->susunDaftar($baris));
    }

    private function cariSampel(int $userId, array $terms, ?int $layananId, ?int $tahun, string $urut)
    {
        $arah = $urut === 'terlama' ? 'asc' : 'desc';
        $kueri = $this->kueriSampel($userId, $terms, $layananId, $tahun);

        if ($urut === 'relevansi') {
            [$skor, $binding] = $this->ekspresiSkor(self::KOLOM_CARI_SAMPEL, $terms);

            $kueri->orderByRaw($skor.' DESC', $binding);
        }

        return $kueri
            ->orderBy('sampel.created_at', $arah)
            ->orderBy('sampel.id', $arah)
            ->select($this->kolomSampel())
            ->paginate(10)
            ->withQueryString()
            ->through(fn ($baris) => $this->susunSampel($baris));
    }

    private function cariGabungan(int $userId, array $terms, ?int $layananId, ?int $tahun, string $urut)
    {
        $arah = $urut === 'terlama' ? 'asc' : 'desc';
        $skorDokumen = $urut === 'relevansi' ? $this->ekspresiSkor(self::KOLOM_CARI, $terms) : ['0', []];
        $skorSampel = $urut === 'relevansi' ? $this->ekspresiSkor(self::KOLOM_CARI_SAMPEL, $terms) : ['0', []];

        $dokumen = $this->kueriDokumen($terms, $layananId, $tahun)
            ->selectRaw(
                "'dokumen' as tipe, dokumen.id as arsip_id, dokumen.created_at as dibuat, ".$skorDokumen[0].' as skor',
                $skorDokumen[1]
            );

        $sampel = $this->kueriSampel($userId, $terms, $layananId, $tahun)
            ->selectRaw(
                "'sampel' as tipe, sampel.id as arsip_id, sampel.created_at as dibuat, ".$skorSampel[0].' as skor',
                $skorSampel[1]
            );

        $gabungan = DB::query()->fromSub($dokumen->unionAll($sampel), 'arsip');

        if ($urut === 'relevansi') {
            $gabungan->orderByDesc('skor');
        }

        $halaman = $gabungan
            ->orderBy('dibuat', $arah)
            ->orderBy('tipe')
            ->orderBy('arsip_id', $arah)
            ->paginate(10)
            ->withQueryString();

        $idDokumen = $halaman->getCollection()->where('tipe', 'dokumen')->pluck('arsip_id')->all();
        $idSampel = $halaman->getCollection()->where('tipe', 'sampel')->pluck('arsip_id')->all();

        $barisDokumen = $idDokumen === []
            ? collect()
            : $this->arsipTersedia()->whereIn('dokumen.id', $idDokumen)->get($this->kolomDaftar())->keyBy('id');

        $barisSampel = $idSampel === []
            ? collect()
            : $this->sampelMilik($userId)->whereIn('sampel.id', $idSampel)->get($this->kolomSampel())->keyBy('id');

        $halaman->through(function ($baris) use ($barisDokumen, $barisSampel) {
            if ($baris->tipe === 'dokumen') {
                $data = $barisDokumen->get($baris->arsip_id);

                return $data ? $this->susunDaftar($data) : null;
            }

            $data = $barisSampel->get($baris->arsip_id);

            return $data ? $this->susunSampel($data) : null;
        });

        $halaman->setCollection($halaman->getCollection()->filter()->values());

        return $halaman;
    }

    private function kueriDokumen(array $terms, ?int $layananId, ?int $tahun): Builder
    {
        $kueri = $this->arsipTersedia()
            ->when($layananId, fn (Builder $q) => $q->where('dokumen.layanan_id', $layananId))
            ->when($tahun, fn (Builder $q) => $q
                ->where('dokumen.tanggal_dokumen', '>=', $tahun.'-01-01')
                ->where('dokumen.tanggal_dokumen', '<', ($tahun + 1).'-01-01'));

        $this->terapkanKataKunci($kueri, array_keys(self::KOLOM_CARI), $terms);

        return $kueri;
    }

    private function kueriSampel(int $userId, array $terms, ?int $layananId, ?int $tahun): Builder
    {
        $kueri = $this->sampelMilik($userId)
            ->when($layananId, fn (Builder $q) => $q->where('sampel.layanan_id', $layananId))
            ->when($tahun, fn (Builder $q) => $q
                ->where('sampel.tanggal_pengambilan', '>=', $tahun.'-01-01')
                ->where('sampel.tanggal_pengambilan', '<', ($tahun + 1).'-01-01'));

        $this->terapkanKataKunci($kueri, array_keys(self::KOLOM_CARI_SAMPEL), $terms);

        return $kueri;
    }

    private function sampelMilik(int $userId): Builder
    {
        return DB::table('sampel')
            ->join('layanan', 'layanan.id', '=', 'sampel.layanan_id')
            ->whereNull('sampel.deleted_at')
            ->where('sampel.diunggah_oleh', $userId);
    }

    private function terapkanKataKunci(Builder $kueri, array $kolom, array $terms): void
    {
        foreach ($terms as $term) {
            $like = '%'.$this->escapeLike($term).'%';

            $kueri->where(function (Builder $q) use ($kolom, $like) {
                foreach ($kolom as $nama) {
                    $q->orWhereRaw($nama." LIKE ? ESCAPE '!'", [$like]);
                }
            });
        }
    }

    private function ekspresiSkor(array $kolom, array $terms): array
    {
        $bagian = [];
        $binding = [];

        foreach ($terms as $term) {
            $like = '%'.$this->escapeLike($term).'%';

            foreach ($kolom as $nama => $bobot) {
                $bagian[] = 'CASE WHEN '.$nama." LIKE ? ESCAPE '!' THEN ".$bobot.' ELSE 0 END';
                $binding[] = $like;
            }
        }

        if ($bagian === []) {
            return ['0', []];
        }

        return ['('.implode(' + ', $bagian).')', $binding];
    }

    private function kolomSampel(): array
    {
        return [
            'sampel.id',
            'sampel.kode_sampel',
            'sampel.nama_sampel',
            'sampel.shipper',
            'sampel.analysis_for',
            'sampel.nama_ruang_arsip',
            'sampel.nomor_ruang_arsip',
            'sampel.created_at',
            'layanan.nama as layanan',
        ];
    }

    private function susunSampel(object $s): array
    {
        return [
            'tipe' => 'sampel',
            'id' => $s->id,
            'kode' => $s->kode_sampel,
            'nama' => $s->nama_sampel,
            'layanan' => $s->layanan,
            'shipper' => $s->shipper,
            'tujuan' => $s->analysis_for,
            'ruang' => $s->nama_ruang_arsip.' (No. '.$s->nomor_ruang_arsip.')',
            'diunggah' => $this->waktuLokal($s->created_at)->translatedFormat('d M Y'),
        ];
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
            'tipe' => 'dokumen',
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

    private function tahunSampel(int $userId): Collection
    {
        return $this->sampelMilik($userId)
            ->distinct()
            ->pluck('sampel.tanggal_pengambilan')
            ->map(fn ($tanggal) => Carbon::parse($tanggal)->year)
            ->unique()
            ->sortDesc()
            ->values();
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