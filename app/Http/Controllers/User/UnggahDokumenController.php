<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Bagian;
use App\Models\Cabang;
use App\Models\KodeSurat;
use Carbon\Carbon;
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
    public function dataForm(object $user): array
    {
        $layanan = $this->layananUser($user->layanan_id);

        return [
            'layanan' => $layanan,
            'jenisList' => $layanan ? $this->jenisUntuk($layanan->id) : collect(),
            'bagianList' => Bagian::orderBy('nama')->get(),
            'kodeSuratList' => KodeSurat::orderBy('kode')->get(),
            'hariIni' => now('Asia/Jakarta')->toDateString(),
        ];
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $layanan = $this->layananUser($user->layanan_id);

        $data = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'bagian_id' => ['required', 'integer', 'exists:bagian,id'],
            'kode_surat_id' => ['nullable', 'integer', 'exists:kode_surat,id'],
            'tanggal_dokumen' => ['required', 'date'],
            'tahun_arsip' => ['nullable', 'string', 'max:50', function ($atribut, $nilai, $gagal) {
                if (! preg_match('/^(?:19|20)\d{2}(?:\s*(?:,|&|dan)\s*(?:19|20)\d{2})*$/i', trim((string) $nilai))) {
                    $gagal('Format tahun tidak valid. Contoh: 2026 atau 2026 dan 2027.');
                }
            }],
            'unit' => ['nullable', 'string', 'max:100'],
            'nomor_rak' => ['nullable', 'string', 'max:30'],
            'no_tingkat' => ['nullable', 'string', 'max:30'],
            'no_box' => ['nullable', 'string', 'max:30'],
            'deskripsi' => ['nullable', 'string'],
            'file_dokumen' => ['required', 'file', 'mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg', 'max:20480'],
        ], [
            'judul.required' => 'Judul dokumen wajib diisi.',
            'bagian_id.required' => 'Bagian wajib dipilih.',
            'bagian_id.exists' => 'Bagian yang dipilih tidak ditemukan.',
            'tanggal_dokumen.required' => 'Tanggal dokumen wajib diisi.',
            'file_dokumen.required' => 'Berkas file dokumen wajib diunggah.',
            'file_dokumen.uploaded' => 'Berkas gagal diunggah. Periksa ukuran berkas lalu coba lagi.',
            'file_dokumen.mimes' => 'Format berkas harus berupa PDF, DOC, DOCX, XLS, XLSX, PNG, atau JPG.',
            'file_dokumen.max' => 'Ukuran berkas file dokumen melebihi batas maksimal 20 MB.',
        ]);

        $bagian = DB::table('bagian')
            ->where('id', $data['bagian_id'])
            ->first(['id', 'kode', 'retensi_tahun']);

        $file = $request->file('file_dokumen');
        $namaAsli = Str::limit(preg_replace('/[\x00-\x1F\x7F]+/', '', $file->getClientOriginalName()), 200, '') ?: 'dokumen';
        $ukuran = (int) $file->getSize();
        $path = $file->store('dokumen', 'public');

        if ($path === false) {
            return back()->withInput()->with('error', 'Berkas gagal disimpan di server. Silakan coba lagi.');
        }

        try {
            $nomor = $this->simpan($user->id, $layanan?->id, $bagian, $data, $path, $namaAsli, $ukuran);
        } catch (Throwable $e) {
            Storage::disk('public')->delete($path);
            report($e);

            return back()->withInput()->with('error', 'Dokumen gagal disimpan. Silakan coba lagi atau hubungi admin.');
        }

        return redirect()->route('user.dokumen-saya.index')
            ->with('success', 'Dokumen berhasil diunggah dengan nomor '.$nomor.' dan langsung tersedia di Cari Arsip.');
    }

    private function simpan(int $userId, ?int $layananId, object $bagian, array $data, string $path, string $namaAsli, int $ukuran): string
    {
        $user = DB::table('users')->where('id', $userId)->first();
        $cabang = $user && $user->cabang_id ? DB::table('cabang')->where('id', $user->cabang_id)->first() : DB::table('cabang')->where('kode', 'CLC')->first();
        $kodeCabang = $cabang ? $cabang->kode : 'CLC';
        $cabangId = $cabang ? $cabang->id : 1;

        $kodeSurat = !empty($data['kode_surat_id']) ? DB::table('kode_surat')->where('id', $data['kode_surat_id'])->first() : null;
        $kodeKlasifikasi = $kodeSurat ? $kodeSurat->kode : $bagian->kode;

        $tanggal = Carbon::parse($data['tanggal_dokumen']);
        $tahun = $tanggal->year;

        $bulanRomawi = match ($tanggal->month) {
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
            default => 'I',
        };

        $retensiTahun = $kodeSurat ? $kodeSurat->retensi_aktif : $bagian->retensi_tahun;
        $retensi = $retensiTahun
            ? $tanggal->copy()->addYears((int) $retensiTahun)->toDateString()
            : null;
        $sekarang = now();

        $nomorRak = $this->bersih($data['nomor_rak'] ?? null);
        $noTingkat = $this->bersih($data['no_tingkat'] ?? null);
        $noBox = $this->bersih($data['no_box'] ?? null);
        $tahunArsip = $this->rapikanTahun($data['tahun_arsip'] ?? null);

        $lokasi = array_filter([
            $nomorRak !== null ? 'Rak '.$nomorRak : null,
            $noTingkat !== null ? 'Tingkat '.$noTingkat : null,
            $noBox !== null ? 'Box '.$noBox : null,
        ]);

        $polaCari = "%/{$kodeKlasifikasi}/{$kodeCabang}/%/{$tahun}";

        for ($percobaan = 1; ; $percobaan++) {
            try {
                return DB::transaction(function () use ($userId, $layananId, $bagian, $kodeSurat, $cabangId, $kodeCabang, $kodeKlasifikasi, $data, $path, $namaAsli, $ukuran, $tanggal, $tahun, $bulanRomawi, $polaCari, $retensi, $sekarang, $nomorRak, $noTingkat, $noBox, $lokasi, $tahunArsip) {
                    $terakhir = DB::table('dokumen')
                        ->where('nomor', 'like', $polaCari)
                        ->orderByDesc('id')
                        ->lockForUpdate()
                        ->value('nomor');

                    $urutan = 1;
                    if ($terakhir) {
                        $bagianNomor = explode('/', $terakhir);
                        $urutan = intval($bagianNomor[0]) + 1;
                    }

                    $nomor = sprintf('%04d/%s/%s/%s/%d', $urutan, $kodeKlasifikasi, $kodeCabang, $bulanRomawi, $tahun);

                    $dokumenId = DB::table('dokumen')->insertGetId([
                        'cabang_id' => $cabangId,
                        'kode_surat_id' => $kodeSurat?->id,
                        'nomor' => $nomor,
                        'judul' => $data['judul'],
                        'bagian_id' => $bagian->id,
                        'layanan_id' => $layananId,
                        'jenis_dokumen_id' => null,
                        'unit' => $this->bersih($data['unit'] ?? null),
                        'tanggal_dokumen' => $tanggal->toDateString(),
                        'tahun_arsip' => $tahunArsip,
                        'deskripsi' => $this->bersih($data['deskripsi'] ?? null),
                        'file_path' => $path,
                        'file_nama_asli' => $namaAsli,
                        'file_ukuran' => $ukuran,
                        'lokasi_arsip_fisik' => $lokasi ? implode(' / ', $lokasi) : null,
                        'nomor_rak' => $nomorRak,
                        'no_tingkat' => $noTingkat,
                        'no_box_file' => $noBox,
                        'diunggah_oleh' => $userId,
                        'status' => 'disetujui',
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
                if ($percobaan >= 5) {
                    throw $e;
                }
            }
        }
    }

    private function rapikanTahun(?string $nilai): ?string
    {
        if (! filled($nilai)) {
            return null;
        }

        preg_match_all('/(?:19|20)\d{2}/', $nilai, $cocok);

        $tahun = collect($cocok[0])->unique()->sort()->values();

        if ($tahun->count() <= 1) {
            return $tahun->first();
        }

        $akhir = $tahun->pop();

        return $tahun->implode(', ').' dan '.$akhir;
    }

    private function bersih(?string $nilai): ?string
    {
        return filled($nilai) ? trim($nilai) : null;
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
}