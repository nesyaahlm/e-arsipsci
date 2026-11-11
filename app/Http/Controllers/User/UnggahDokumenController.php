<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;

class UnggahDokumenController extends Controller
{
    private const LABEL_STATUS = [
        'menunggu' => 'Menunggu Verifikasi',
        'disetujui' => 'Disetujui',
        'ditolak' => 'Ditolak',
    ];

    public function create(Request $request): View
    {
        $user = $request->user();
        $layanan = $this->layananUser($user->layanan_id);

        return view('user.unggah.create', [
            'layanan' => $layanan,
            'jenisList' => $layanan ? $this->jenisUntuk($layanan->id) : collect(),
            'unitList' => $this->unitList(),
            'terakhir' => $this->unggahanTerakhir($user->id),
            'hariIni' => now('Asia/Jakarta')->toDateString(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $layanan = $this->layananUser($user->layanan_id);

        if ($layanan === null) {
            return back()->with('error', 'Akun Anda belum terhubung ke layanan. Hubungi admin untuk mengaturnya.');
        }

        $data = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'jenis_dokumen_id' => [
                'required',
                'integer',
                Rule::exists('jenis_dokumen_layanan', 'jenis_dokumen_id')->where('layanan_id', $layanan->id),
            ],
            'tanggal_dokumen' => ['required', 'date', 'before_or_equal:'.now('Asia/Jakarta')->toDateString()],
            'unit' => ['nullable', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string', 'max:2000'],
            'file_dokumen' => ['required', 'file', 'mimes:pdf,doc,docx,xls,xlsx', 'max:20480'],
        ], [
            'judul.required' => 'Judul dokumen wajib diisi.',
            'judul.max' => 'Judul dokumen maksimal 255 karakter.',
            'jenis_dokumen_id.required' => 'Jenis dokumen wajib dipilih.',
            'jenis_dokumen_id.exists' => 'Jenis dokumen tidak tersedia untuk layanan Anda.',
            'tanggal_dokumen.required' => 'Tanggal dokumen wajib diisi.',
            'tanggal_dokumen.date' => 'Format tanggal dokumen tidak valid.',
            'tanggal_dokumen.before_or_equal' => 'Tanggal dokumen tidak boleh melewati hari ini.',
            'unit.max' => 'Unit kerja maksimal 100 karakter.',
            'deskripsi.max' => 'Deskripsi maksimal 2000 karakter.',
            'file_dokumen.required' => 'Berkas dokumen wajib dipilih.',
            'file_dokumen.uploaded' => 'Berkas gagal diunggah. Periksa ukuran berkas lalu coba lagi.',
            'file_dokumen.mimes' => 'Format berkas harus PDF, Word (DOC, DOCX), atau Excel (XLS, XLSX).',
            'file_dokumen.max' => 'Ukuran berkas maksimal 20 MB.',
        ]);

        $jenis = DB::table('jenis_dokumen')
            ->where('id', $data['jenis_dokumen_id'])
            ->first(['id', 'kode', 'retensi_tahun']);

        $file = $request->file('file_dokumen');
        $namaAsli = Str::limit(preg_replace('/[\x00-\x1F\x7F]+/', '', $file->getClientOriginalName()), 200, '') ?: 'dokumen';
        $ukuran = (int) $file->getSize();
        $path = $file->store('dokumen', 'public');

        if ($path === false) {
            return back()->withInput()->with('error', 'Berkas gagal disimpan di server. Silakan coba lagi.');
        }

        try {
            $nomor = $this->simpan($user->id, $layanan->id, $jenis, $data, $path, $namaAsli, $ukuran);
        } catch (Throwable $e) {
            Storage::disk('public')->delete($path);
            report($e);

            return back()->withInput()->with('error', 'Dokumen gagal disimpan. Silakan coba lagi atau hubungi admin.');
        }

        return redirect()->route('user.unggah.create')
            ->with('success', 'Dokumen berhasil dikirim dengan nomor '.$nomor.' dan menunggu pemeriksaan admin.');
    }

    private function simpan(int $userId, int $layananId, object $jenis, array $data, string $path, string $namaAsli, int $ukuran): string
    {
        $tanggal = Carbon::parse($data['tanggal_dokumen']);
        $prefix = $jenis->kode.'/CLC/'.$tanggal->year.'/';
        $retensi = $jenis->retensi_tahun
            ? $tanggal->copy()->addYears((int) $jenis->retensi_tahun)->toDateString()
            : null;
        $sekarang = now();

        for ($percobaan = 1; ; $percobaan++) {
            try {
                return DB::transaction(function () use ($userId, $layananId, $jenis, $data, $path, $namaAsli, $ukuran, $tanggal, $prefix, $retensi, $sekarang) {
                    $terakhir = DB::table('dokumen')
                        ->where('nomor', 'like', $prefix.'%')
                        ->orderByDesc('nomor')
                        ->lockForUpdate()
                        ->value('nomor');

                    $urutan = $terakhir ? ((int) substr($terakhir, strlen($prefix))) + 1 : 1;
                    $nomor = sprintf('%s%04d', $prefix, $urutan);

                    $dokumenId = DB::table('dokumen')->insertGetId([
                        'nomor' => $nomor,
                        'judul' => $data['judul'],
                        'layanan_id' => $layananId,
                        'jenis_dokumen_id' => $jenis->id,
                        'unit' => filled($data['unit'] ?? null) ? trim($data['unit']) : null,
                        'tanggal_dokumen' => $tanggal->toDateString(),
                        'deskripsi' => filled($data['deskripsi'] ?? null) ? trim($data['deskripsi']) : null,
                        'file_path' => $path,
                        'file_nama_asli' => $namaAsli,
                        'file_ukuran' => $ukuran,
                        'diunggah_oleh' => $userId,
                        'status' => 'menunggu',
                        'retensi_sampai' => $retensi,
                        'created_at' => $sekarang,
                        'updated_at' => $sekarang,
                    ]);

                    DB::table('log_aktivitas')->insert([
                        'user_id' => $userId,
                        'aktivitas' => 'mengunggah',
                        'dokumen_id' => $dokumenId,
                        'keterangan' => null,
                        'created_at' => $sekarang,
                    ]);

                    return $nomor;
                });
            } catch (UniqueConstraintViolationException $e) {
                if ($percobaan >= 3) {
                    throw $e;
                }
            }
        }
    }

    private function layananUser(?int $layananId): ?object
    {
        if ($layananId === null) {
            return null;
        }

        return DB::table('layanan')->where('id', $layananId)->first(['id', 'nama']);
    }

    private function jenisUntuk(int $layananId): Collection
    {
        return DB::table('jenis_dokumen')
            ->join('jenis_dokumen_layanan', 'jenis_dokumen_layanan.jenis_dokumen_id', '=', 'jenis_dokumen.id')
            ->where('jenis_dokumen_layanan.layanan_id', $layananId)
            ->orderBy('jenis_dokumen.nama')
            ->get(['jenis_dokumen.id', 'jenis_dokumen.kode', 'jenis_dokumen.nama']);
    }

    private function unitList(): Collection
    {
        return DB::table('dokumen')
            ->whereNull('deleted_at')
            ->whereNotNull('unit')
            ->where('unit', '!=', '')
            ->distinct()
            ->orderBy('unit')
            ->pluck('unit');
    }

    private function unggahanTerakhir(int $userId): Collection
    {
        return DB::table('dokumen')
            ->join('jenis_dokumen', 'jenis_dokumen.id', '=', 'dokumen.jenis_dokumen_id')
            ->whereNull('dokumen.deleted_at')
            ->where('dokumen.diunggah_oleh', $userId)
            ->orderByDesc('dokumen.created_at')
            ->orderByDesc('dokumen.id')
            ->limit(3)
            ->get([
                'dokumen.id',
                'dokumen.judul',
                'dokumen.status',
                'dokumen.created_at',
                'jenis_dokumen.nama as jenis',
            ])
            ->map(function ($d) {
                $waktu = Carbon::parse($d->created_at, config('app.timezone'))
                    ->setTimezone('Asia/Jakarta')
                    ->locale('id');

                return [
                    'id' => $d->id,
                    'judul' => $d->judul,
                    'jenis' => $d->jenis,
                    'status' => $d->status,
                    'status_label' => self::LABEL_STATUS[$d->status] ?? ucfirst((string) $d->status),
                    'tanggal' => $waktu->translatedFormat('d M Y').' · '.$waktu->format('H:i').' WIB',
                ];
            });
    }
}