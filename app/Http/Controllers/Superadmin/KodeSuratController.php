<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Bagian;
use App\Models\KodeSurat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KodeSuratController extends Controller
{
    public function index(Request $request): View
    {
        $cari = trim((string) $request->input('cari'));
        $bagianId = $request->input('bagian_id');

        $daftarKodeSurat = KodeSurat::with('bagian')
            ->when($cari !== '', function ($query) use ($cari) {
                $query->where(function ($q) use ($cari) {
                    $q->where('kode', 'like', "%{$cari}%")
                      ->orWhere('nama', 'like', "%{$cari}%")
                      ->orWhere('keterangan', 'like', "%{$cari}%")
                      ->orWhereHas('bagian', function ($b) use ($cari) {
                          $b->where('nama', 'like', "%{$cari}%");
                      });
                });
            })
            ->when($bagianId, function ($query) use ($bagianId) {
                $query->where('bagian_id', $bagianId);
            })
            ->orderBy('kode')
            ->paginate(10)
            ->withQueryString();

        $daftarBagian = Bagian::orderBy('nama')->get();

        return view('superadmin.kode-surat.index', compact('daftarKodeSurat', 'daftarBagian', 'cari', 'bagianId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge(['kode' => strtoupper(trim((string) $request->input('kode')))]);

        $data = $request->validate([
            'bagian_id' => ['required', 'exists:bagian,id'],
            'kode' => ['required', 'string', 'max:20', 'unique:kode_surat,kode'],
            'nama' => ['required', 'string', 'max:255'],
            'retensi_aktif' => ['required', 'integer', 'min:1', 'max:50'],
            'retensi_inaktif' => ['required', 'integer', 'min:0', 'max:50'],
            'keterangan' => ['nullable', 'string'],
        ], [
            'bagian_id.required' => 'Bagian wajib dipilih.',
            'bagian_id.exists' => 'Bagian yang dipilih tidak valid.',
            'kode.required' => 'Kode surat wajib diisi.',
            'kode.unique' => 'Kode surat sudah terdaftar.',
            'nama.required' => 'Nama klasifikasi surat wajib diisi.',
            'retensi_aktif.required' => 'Masa retensi aktif wajib diisi.',
            'retensi_inaktif.required' => 'Masa retensi inaktif wajib diisi.',
        ]);

        KodeSurat::create($data);

        return redirect()
            ->route('superadmin.kode-surat.index')
            ->with('sukses', 'Kode surat berhasil ditambahkan.');
    }

    public function update(Request $request, KodeSurat $kodeSurat): RedirectResponse
    {
        $request->merge(['kode' => strtoupper(trim((string) $request->input('kode')))]);

        $data = $request->validate([
            'bagian_id' => ['required', 'exists:bagian,id'],
            'kode' => ['required', 'string', 'max:20', Rule::unique('kode_surat', 'kode')->ignore($kodeSurat->id)],
            'nama' => ['required', 'string', 'max:255'],
            'retensi_aktif' => ['required', 'integer', 'min:1', 'max:50'],
            'retensi_inaktif' => ['required', 'integer', 'min:0', 'max:50'],
            'keterangan' => ['nullable', 'string'],
        ], [
            'bagian_id.required' => 'Bagian wajib dipilih.',
            'bagian_id.exists' => 'Bagian yang dipilih tidak valid.',
            'kode.required' => 'Kode surat wajib diisi.',
            'kode.unique' => 'Kode surat sudah terdaftar.',
            'nama.required' => 'Nama klasifikasi surat wajib diisi.',
            'retensi_aktif.required' => 'Masa retensi aktif wajib diisi.',
            'retensi_inaktif.required' => 'Masa retensi inaktif wajib diisi.',
        ]);

        $kodeSurat->update($data);

        return redirect()
            ->route('superadmin.kode-surat.index')
            ->with('sukses', 'Kode surat berhasil diperbarui.');
    }

    public function destroy(KodeSurat $kodeSurat): RedirectResponse
    {
        $kode = $kodeSurat->kode;
        $kodeSurat->delete();

        return redirect()
            ->route('superadmin.kode-surat.index')
            ->with('sukses', 'Kode surat ' . $kode . ' berhasil dihapus.');
    }
}