<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Sampel;
use App\Models\Bagian;

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

        $sampel = $this->kueriCari($kata)
            ->with('bagian:id,nama')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $jumlahLabel = $this->kueriCari($kata)->count();

        $bagianList = Bagian::orderBy('nama')->get();

        return view('user.sampel.index', compact('sampel', 'kata', 'bagianList', 'jumlahLabel'));
    }

    public function show(int $sampel)
    {
        $data = Sampel::with('bagian:id,nama')
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
        // Kembali ke Cari Arsip (lengkap dengan filternya) bila user datang dari sana.
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
        $data = Sampel::with('bagian:id,nama')
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

        $daftar = Sampel::with('bagian:id,nama')
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
        $pdf->setPaper('A5', 'landscape');
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
            ->with('bagian:id,nama')
            ->where('diunggah_oleh', Auth::id())
            ->when($kata !== '', function (Builder $query) use ($kata) {
                $like = '%'.str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $kata).'%';

                $query->where(function (Builder $cari) use ($like) {
                    $cari->whereRaw("kode_sampel LIKE ? ESCAPE '!'", [$like])
                        ->orWhereRaw("nama_sampel LIKE ? ESCAPE '!'", [$like])
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
        $bagianList = Bagian::orderBy('nama')->get();
        return view('user.sampel.create', compact('bagianList'));
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
            'analysis_for' => ['required', 'string', 'max:255'],
            'bagian_id' => ['required', 'exists:bagian,id'],
            'nomor_ruang_arsip' => ['required', 'string', 'max:50'],
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
            'analysis_for.required' => 'Tujuan analisis wajib diisi.',
            'bagian_id.required' => 'bagian wajib dipilih.',
            'bagian_id.exists' => 'bagian yang dipilih tidak valid.',
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

        $sampel = Sampel::create([
            'kode_sampel' => $validated['kode_sampel'],
            'nama_sampel' => $validated['nama_sampel'],
            'shipper' => $validated['shipper'] ?? null,
            'vessel' => $validated['vessel'] ?? null,
            'bill_of_lading' => $validated['bill_of_lading'] ?? null,
            'tanggal_pengambilan' => $validated['tanggal_pengambilan'],
            'tempat_pengambilan' => $validated['tempat_pengambilan'],
            'analysis_for' => $validated['analysis_for'],
            'bagian_id' => $validated['bagian_id'],
            'nomor_ruang_arsip' => $validated['nomor_ruang_arsip'],
            'nama_ruang_arsip' => $validated['nama_ruang_arsip'],
            'tanggal_masuk_arsip' => $validated['tanggal_masuk_arsip'],
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