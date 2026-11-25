<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PemusnahanItem;
use App\Models\Sampel;
use App\Models\Bagian;
use App\Models\Portofolio;
use App\Models\KodeSurat;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class SampelController extends Controller
{
    private const BATAS_LABEL = 100;

    public function index(Request $request)
    {
        $kata = trim((string) $request->query('q', ''));
        $userCabangId = Auth::user()->cabang_id ?? 1;

        $sampel = $this->kueriCari($kata)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $jumlahLabel = $this->kueriCari($kata)->count();

        $lokasiSemula = PemusnahanItem::query()
            ->whereIn('sampel_id', $sampel->pluck('id'))
            ->pluck('lokasi_sebelumnya', 'sampel_id')
            ->map(fn ($lokasi) => trim((string) $lokasi, " \t\n\r\0\x0B/-"))
            ->filter(fn ($lokasi) => $lokasi !== '');

        $portofolioList = Portofolio::where(function ($q) use ($userCabangId) {
            $q->whereNull('cabang_id')
                ->orWhere('cabang_id', $userCabangId);
        })->orderBy('nama')->get();

        $kodeSuratList = KodeSurat::whereNotNull('portofolio_id')->orderBy('kode')->get();

        return view('user.sampel.index', compact('sampel', 'kata', 'portofolioList', 'kodeSuratList', 'jumlahLabel', 'lokasiSemula'));
    }

    public function show(int $sampel)
    {
        $data = Sampel::with(['portofolio:id,nama', 'bagian:id,nama'])
            ->where('diunggah_oleh', Auth::id())
            ->findOrFail($sampel);

        $fotoAda = filled($data->foto_path)
            && Storage::disk('public')->exists($data->foto_path);

        return view('user.sampel.show', [
            'sampel' => $data,
            'fotoAda' => $fotoAda,
            'kembali' => $this->urlKembali(),
        ]);
    }

    private function urlKembali(): string
    {
        $arsip = route('user.arsip.index');
        $sebelumnya = url()->previous();

        if (
            parse_url($sebelumnya, PHP_URL_PATH) === parse_url($arsip, PHP_URL_PATH)
            && parse_url($sebelumnya, PHP_URL_HOST) === parse_url($arsip, PHP_URL_HOST)
        ) {
            return $sebelumnya;
        }

        return route('user.sampel.index');
    }

    public function labelPdf(int $sampel): Response
    {
        $data = Sampel::with(['portofolio:id,nama', 'bagian:id,nama'])
            ->where('diunggah_oleh', Auth::id())
            ->findOrFail($sampel);

        $nama = trim((string) preg_replace('/[^A-Za-z0-9]+/', '-', (string) $data->kode_sampel), '-');
        $nama = 'Label-Sampel-'.($nama !== '' ? $nama : $data->id).'.pdf';

        return $this->cetakLabel(collect([$data]), $nama);
    }

    public function labelSemua(Request $request): Response|RedirectResponse
    {
        $kata = trim((string) $request->query('q', ''));

        $daftar = $this->kueriCari($kata)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(self::BATAS_LABEL)
            ->get();

        if ($daftar->isEmpty()) {
            return redirect()->route('user.sampel.index')
                ->with('label_error', 'Belum ada sampel untuk dibuatkan label.');
        }

        $nama = 'Label-Sampel-Semua-'.now('Asia/Jakarta')->format('Ymd-His').'.pdf';

        return $this->cetakLabel($daftar, $nama);
    }

    public function labelTerpilih(Request $request): Response|RedirectResponse
    {
        $ids = collect($request->input('ids', []))
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return redirect()->route('user.sampel.index')
                ->with('label_error', 'Pilih minimal satu sampel untuk diunduh labelnya.');
        }

        if ($ids->count() > self::BATAS_LABEL) {
            return redirect()->route('user.sampel.index')
                ->with('label_error', 'Maksimal '.self::BATAS_LABEL.' label dalam sekali unduh.');
        }

        $daftar = Sampel::with(['portofolio:id,nama', 'bagian:id,nama'])
            ->where('diunggah_oleh', Auth::id())
            ->whereIn('id', $ids)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        if ($daftar->isEmpty()) {
            return redirect()->route('user.sampel.index')
                ->with('label_error', 'Sampel yang dipilih tidak ditemukan.');
        }

        $nama = 'Label-Sampel-Terpilih-'.now('Asia/Jakarta')->format('Ymd-His').'.pdf';

        return $this->cetakLabel($daftar, $nama);
    }

    private function cetakLabel(Collection $daftar, string $nama): Response
    {
        @set_time_limit(120);

        $this->catatCetak($daftar);

        $html = view('user.sampel.label-pdf', ['daftar' => $daftar])->render();

        $opsi = new Options;
        $opsi->set('isRemoteEnabled', false);
        $opsi->set('defaultFont', 'DejaVu Sans');

        $pdf = new Dompdf($opsi);
        $pdf->loadHtml($html, 'UTF-8');
        $pdf->setPaper('A5', 'portrait');
        $pdf->render();

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$nama.'"',
        ]);
    }

    private function catatCetak(Collection $daftar): void
    {
        try {
            $sekarang = now();

            $baris = $daftar->map(fn (Sampel $item) => [
                'user_id' => Auth::id(),
                'aktivitas' => 'mencetak_label_sampel',
                'dokumen_id' => null,
                'sampel_id' => $item->id,
                'keterangan' => Str::limit('Mengunduh label sampel '.$item->nama_sampel, 250, ''),
                'created_at' => $sekarang,
            ])->all();

            DB::table('log_aktivitas')->insert($baris);
        } catch (Throwable $e) {
            report($e);
        }
    }

    private function kueriCari(string $kata): Builder
    {
        return Sampel::query()
            ->with(['portofolio:id,nama', 'bagian:id,nama'])
            ->where('diunggah_oleh', Auth::id())
            ->when($kata !== '', function (Builder $query) use ($kata) {
                $like = '%'.str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $kata).'%';

                $query->where(function (Builder $cari) use ($like) {
                    $cari->whereRaw("kode_sampel LIKE ? ESCAPE '!'", [$like])
                        ->orWhereRaw("nama_sampel LIKE ? ESCAPE '!'", [$like])
                        ->orWhereRaw("keterangan_1 LIKE ? ESCAPE '!'", [$like])
                        ->orWhereRaw("keterangan_2 LIKE ? ESCAPE '!'", [$like])
                        ->orWhereRaw("keterangan_3 LIKE ? ESCAPE '!'", [$like])
                        ->orWhereRaw("analysis_for LIKE ? ESCAPE '!'", [$like]);
                });
            });
    }

    public function foto(Request $request, int $sampel)
    {
        $data = Sampel::where('diunggah_oleh', Auth::id())->findOrFail($sampel);

        abort_unless(filled($data->foto_path) && Storage::disk('public')->exists($data->foto_path), 404);

        $ekstensi = strtolower(pathinfo($data->foto_path, PATHINFO_EXTENSION));

        abort_unless(in_array($ekstensi, ['jpg', 'jpeg', 'png'], true), 404);

        $berkas = Storage::disk('public')->path($data->foto_path);

        if ($request->boolean('unduh')) {
            $nama = trim((string) preg_replace('/[^A-Za-z0-9]+/', '-', (string) $data->kode_sampel), '-');
            $nama = 'Foto-Sampel-'.($nama !== '' ? $nama : $data->id).'.'.($ekstensi === 'jpeg' ? 'jpg' : $ekstensi);

            return response()->download($berkas, $nama, [
                'Content-Type' => $ekstensi === 'png' ? 'image/png' : 'image/jpeg',
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }

        return response()->file($berkas, [
            'Content-Type' => $ekstensi === 'png' ? 'image/png' : 'image/jpeg',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-cache, must-revalidate',
        ]);
    }

    public function create()
    {
        $userCabangId = Auth::user()->cabang_id ?? 1;

        $portofolioList = Portofolio::where(function ($q) use ($userCabangId) {
            $q->whereNull('cabang_id')
                ->orWhere('cabang_id', $userCabangId);
        })->orderBy('nama')->get();

        $kodeSuratList = KodeSurat::whereNotNull('portofolio_id')->orderBy('kode')->get();

        return view('user.sampel.create', compact('portofolioList', 'kodeSuratList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_sampel' => ['required', 'string', 'max:50', 'unique:sampel,kode_sampel'],
            'nama_sampel' => ['required', 'string', 'max:255'],
            'shipper' => ['nullable', 'string', 'max:255'],
            'vessel' => ['nullable', 'string', 'max:255'],
            'bill_of_lading' => ['nullable', 'string', 'max:255'],
            'tanggal_pengambilan' => ['required', 'date'],
            'tempat_pengambilan' => ['required', 'string', 'max:255'],
            'keterangan_1' => ['nullable', 'string', 'max:255'],
            'keterangan_2' => ['nullable', 'string', 'max:255'],
            'keterangan_3' => ['nullable', 'string', 'max:255'],
            'analysis_for' => ['nullable', 'string', 'max:255'],
            'portofolio_id' => ['required', 'exists:portofolio,id'],
            'kode_surat_id' => ['nullable', 'exists:kode_surat,id'],
            'nomor_ruang_arsip' => ['required', 'string', 'max:50'],
            'nomor_tingkat_arsip' => ['nullable', 'string', 'max:50'],
            'tahun_arsip' => Sampel::aturanTahun(),
            'nama_ruang_arsip' => ['required', 'string', 'max:100'],
            'tanggal_masuk_arsip' => ['required', 'date'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:5120'],
            'deskripsi' => ['nullable', 'string'],
        ], [
            'kode_sampel.required' => 'Kode sampel wajib diisi.',
            'kode_sampel.unique' => 'Kode sampel sudah terdaftar, gunakan kode lain.',
            'nama_sampel.required' => 'Nama sampel / komoditas wajib diisi.',
            'tanggal_pengambilan.required' => 'Tanggal pengambilan sampel wajib diisi.',
            'tempat_pengambilan.required' => 'Tempat pengambilan sampel wajib diisi.',
            'portofolio_id.required' => 'Portofolio wajib dipilih.',
            'portofolio_id.exists' => 'Portofolio yang dipilih tidak valid.',
            'nomor_ruang_arsip.required' => 'Nomor ruang/rak arsip wajib diisi.',
            'nama_ruang_arsip.required' => 'Nama ruang arsip sampel wajib diisi.',
            'tanggal_masuk_arsip.required' => 'Tanggal masuk arsip wajib diisi.',
            'foto.image' => 'Berkas harus berupa gambar.',
            'foto.mimes' => 'Format foto harus berupa JPG, JPEG, atau PNG.',
            'foto.max' => 'Ukuran foto maksimal 5 MB.',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('sampel', 'public');
        }

        $portofolio = Portofolio::find($validated['portofolio_id']);
        $status = 'aktif';
        if ($portofolio && !empty($portofolio->retensi_tahun) && !empty($validated['tanggal_masuk_arsip'])) {
            $tgl = Carbon::parse($validated['tanggal_masuk_arsip']);
            $kadaluwarsa = (($portofolio->retensi_satuan ?? 'bulan') === 'bulan')
                ? $tgl->addMonths($portofolio->retensi_tahun)
                : $tgl->addYears($portofolio->retensi_tahun);
            $status = $kadaluwarsa->isPast() ? 'inaktif' : 'aktif';
        }

        $sampel = Sampel::create([
            'cabang_id' => Auth::user()->cabang_id ?? 1,
            'kode_sampel' => $validated['kode_sampel'],
            'nama_sampel' => $validated['nama_sampel'],
            'shipper' => $validated['shipper'] ?? null,
            'vessel' => $validated['vessel'] ?? null,
            'bill_of_lading' => $validated['bill_of_lading'] ?? null,
            'tanggal_pengambilan' => $validated['tanggal_pengambilan'],
            'tempat_pengambilan' => $validated['tempat_pengambilan'],
            'keterangan_1' => $validated['keterangan_1'] ?? null,
            'keterangan_2' => $validated['keterangan_2'] ?? null,
            'keterangan_3' => $validated['keterangan_3'] ?? null,
            'analysis_for' => $validated['analysis_for'] ?? null,
            'portofolio_id' => $validated['portofolio_id'],
            'kode_surat_id' => $validated['kode_surat_id'] ?? null,
            'nomor_ruang_arsip' => $validated['nomor_ruang_arsip'],
            'nomor_tingkat_arsip' => $validated['nomor_tingkat_arsip'] ?? null,
            'tahun_arsip' => Sampel::rapikanTahun($validated['tahun_arsip'] ?? null),
            'nama_ruang_arsip' => $validated['nama_ruang_arsip'],
            'tanggal_masuk_arsip' => $validated['tanggal_masuk_arsip'],
            'status' => $status,
            'foto_path' => $fotoPath,
            'deskripsi' => $validated['deskripsi'] ?? null,
            'diunggah_oleh' => Auth::id(),
        ]);

        DB::table('log_aktivitas')->insert([
            'user_id' => Auth::id(),
            'aktivitas' => 'mengunggah_sampel',
            'dokumen_id' => null,
            'sampel_id' => $sampel->id,
            'keterangan' => 'Mencatat sampel baru ' . $sampel->nama_sampel,
            'created_at' => now(),
        ]);

        return redirect()->route('user.sampel.index')->with('success', 'Data sampel ' . $sampel->kode_sampel . ' berhasil dicatat!');
    }
}