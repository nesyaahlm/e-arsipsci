<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use App\Models\Sampel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SampelController extends Controller
{
    public function index(Request $request): View
    {
        $cari = trim((string) $request->input('cari'));

        $sampel = Sampel::with(['layanan', 'pengunggah'])
            ->when($cari !== '', function ($query) use ($cari) {
                $query->where(function ($q) use ($cari) {
                    $kolom = [
                        'kode_sampel',
                        'nama_sampel',
                        'shipper',
                        'vessel',
                        'bill_of_lading',
                        'tempat_pengambilan',
                        'analysis_for',
                        'nomor_ruang_arsip',
                        'nama_ruang_arsip',
                        'deskripsi',
                        'tanggal_pengambilan',
                        'tanggal_masuk_arsip',
                    ];

                    foreach ($kolom as $nama) {
                        $q->orWhere($nama, 'like', "%{$cari}%");
                    }

                    $q->orWhereHas('layanan', function ($l) use ($cari) {
                        $l->where('nama', 'like', "%{$cari}%");
                    })->orWhereHas('pengunggah', function ($u) use ($cari) {
                        $u->where('name', 'like', "%{$cari}%");
                    });
                });
            })
            ->when($request->filled('layanan'), function ($query) use ($request) {
                $query->where('layanan_id', $request->input('layanan'));
            })
            ->orderByDesc('tanggal_masuk_arsip')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.sampel.index', [
            'sampel' => $sampel,
            'daftarLayanan' => Layanan::orderBy('nama')->get(),
        ]);
    }

    public function show(Sampel $sampel): View
    {
        $sampel->load(['layanan', 'pengunggah']);

        DB::table('log_aktivitas')->insert([
            'user_id' => Auth::id(),
            'aktivitas' => 'melihat_sampel',
            'dokumen_id' => null,
            'sampel_id' => $sampel->id,
            'keterangan' => $sampel->nama_sampel,
            'created_at' => now(),
        ]);

        return view('admin.sampel.show', ['sampel' => $sampel]);
    }

    public function create(): View
    {
        $layananList = Layanan::orderBy('nama')->get();

        return view('admin.sampel.create', compact('layananList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->aturan(), $this->pesan('Kode sampel sudah terdaftar, gunakan kode lain.'));

        $data = collect($validated)->except('foto')->all();
        $data['foto_path'] = $request->hasFile('foto')
            ? $request->file('foto')->store('sampel', 'public')
            : null;
        $data['diunggah_oleh'] = Auth::id();

        $sampel = Sampel::create($data);

        DB::table('log_aktivitas')->insert([
            'user_id' => Auth::id(),
            'aktivitas' => 'mengunggah_sampel',
            'dokumen_id' => null,
            'sampel_id' => $sampel->id,
            'keterangan' => 'Menambahkan sampel ' . $sampel->nama_sampel,
            'created_at' => now(),
        ]);

        return redirect()
            ->route('admin.sampel.index')
            ->with('success', 'Data sampel ' . $sampel->kode_sampel . ' berhasil disimpan!');
    }

    public function edit($id): View
    {
        $sampel = Sampel::findOrFail($id);
        $layananList = Layanan::orderBy('nama')->get();

        return view('admin.sampel.edit', compact('sampel', 'layananList'));
    }

    public function update(Request $request, $id)
    {
        $sampel = Sampel::findOrFail($id);

        $validated = $request->validate($this->aturan((int) $id), $this->pesan('Kode sampel sudah digunakan sampel lain.'));

        $data = collect($validated)->except('foto')->all();
        $data['foto_path'] = $sampel->foto_path;

        if ($request->hasFile('foto')) {
            if ($sampel->foto_path && Storage::disk('public')->exists($sampel->foto_path)) {
                Storage::disk('public')->delete($sampel->foto_path);
            }
            $data['foto_path'] = $request->file('foto')->store('sampel', 'public');
        }

        $sampel->update($data);

        DB::table('log_aktivitas')->insert([
            'user_id' => Auth::id(),
            'aktivitas' => 'mengubah_sampel',
            'dokumen_id' => null,
            'sampel_id' => $sampel->id,
            'keterangan' => 'Memperbarui data sampel ' . $sampel->nama_sampel,
            'created_at' => now(),
        ]);

        return redirect()
            ->route('admin.sampel.index')
            ->with('success', 'Data sampel ' . $sampel->kode_sampel . ' berhasil diperbarui!');
    }

    public function destroy(Sampel $sampel)
    {
        DB::table('log_aktivitas')->insert([
            'user_id' => Auth::id(),
            'aktivitas' => 'menghapus_sampel',
            'dokumen_id' => null,
            'sampel_id' => $sampel->id,
            'keterangan' => 'Menghapus sampel ' . $sampel->nama_sampel,
            'created_at' => now(),
        ]);

        $kode = $sampel->kode_sampel;
        $sampel->delete();

        return redirect()
            ->route('admin.sampel.index')
            ->with('success', 'Sampel ' . $kode . ' berhasil dihapus.');
    }

    private function aturan(?int $abaikanId = null): array
    {
        return [
            'kode_sampel' => ['required', 'string', 'max:50', 'unique:sampel,kode_sampel' . ($abaikanId ? ',' . $abaikanId : '')],
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
        ];
    }

    private function pesan(string $pesanUnik): array
    {
        return [
            'kode_sampel.required' => 'Kode sampel wajib diisi.',
            'kode_sampel.unique' => $pesanUnik,
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
        ];
    }
}