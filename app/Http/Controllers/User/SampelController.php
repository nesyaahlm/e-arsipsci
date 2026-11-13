<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Sampel;
use App\Models\Layanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SampelController extends Controller
{
    public function index(Request $request)
    {
        $kata = trim((string) $request->query('q', ''));

        $sampel = Sampel::with('layanan:id,nama')
            ->where('diunggah_oleh', Auth::id())
            ->when($kata !== '', function ($query) use ($kata) {
                $like = '%'.str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $kata).'%';

                $query->where(function ($cari) use ($like) {
                    $cari->whereRaw("kode_sampel LIKE ? ESCAPE '!'", [$like])
                        ->orWhereRaw("nama_sampel LIKE ? ESCAPE '!'", [$like])
                        ->orWhereRaw("analysis_for LIKE ? ESCAPE '!'", [$like]);
                });
            })
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $layananList = Layanan::orderBy('nama')->get();

        return view('user.sampel.index', compact('sampel', 'kata', 'layananList'));
    }

    public function show(int $sampel)
    {
        $data = Sampel::with('layanan:id,nama')
            ->where('diunggah_oleh', Auth::id())
            ->findOrFail($sampel);

        $fotoAda = filled($data->foto_path)
            && Storage::disk('public')->exists($data->foto_path);

        return view('user.sampel.show', [
            'sampel' => $data,
            'fotoAda' => $fotoAda,
        ]);
    }

    public function foto(int $sampel)
    {
        $data = Sampel::where('diunggah_oleh', Auth::id())->findOrFail($sampel);

        abort_unless(filled($data->foto_path) && Storage::disk('public')->exists($data->foto_path), 404);

        $ekstensi = strtolower(pathinfo($data->foto_path, PATHINFO_EXTENSION));

        abort_unless(in_array($ekstensi, ['jpg', 'jpeg', 'png'], true), 404);

        return response()->file(Storage::disk('public')->path($data->foto_path), [
            'Content-Type' => $ekstensi === 'png' ? 'image/png' : 'image/jpeg',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-cache, must-revalidate',
        ]);
    }

    public function create()
    {
        $layananList = Layanan::orderBy('nama')->get();
        return view('user.sampel.create', compact('layananList'));
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
            'layanan_id' => ['required', 'exists:layanan,id'],
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
            'layanan_id.required' => 'Layanan wajib dipilih.',
            'layanan_id.exists' => 'Layanan yang dipilih tidak valid.',
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
            'layanan_id' => $validated['layanan_id'],
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