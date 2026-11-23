<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Bagian;
use App\Models\Cabang;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BagianController extends Controller
{
    public function index(Request $request): View
    {
        $cari = $request->query('cari');
        $cabangId = $request->query('cabang_id', 'nasional');

        $query = Bagian::with(['cabang'])
            ->withCount(['kodeSurat', 'dokumen']);

        if (!empty($cari)) {
            $query->where(function ($q) use ($cari) {
                $q->where('kode', 'like', "%{$cari}%")
                    ->orWhere('nama', 'like', "%{$cari}%");
            });
        }

        if ($cabangId === 'nasional' || empty($cabangId)) {
            $query->whereNull('cabang_id');
        } elseif ($cabangId !== 'semua') {
            $query->where('cabang_id', $cabangId);
        }

        $daftarBagian = $query->orderBy('nama', 'asc')
            ->paginate(10)
            ->withQueryString();

        $daftarCabang = Cabang::orderBy('nama', 'asc')->get();

        return view('superadmin.bagian.index', compact('daftarBagian', 'daftarCabang', 'cari', 'cabangId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'cabang_id' => ['nullable', 'exists:cabang,id'],
            'kode' => ['required', 'string', 'max:20', 'unique:bagian,kode'],
            'nama' => ['required', 'string', 'max:255'],
            'retensi_tahun' => ['nullable', 'integer', 'min:1', 'max:100'],
            'retensi_satuan' => ['nullable', 'in:tahun,bulan'],
        ]);

        $validated['kode'] = strtoupper($validated['kode']);

        Bagian::create($validated);

        return redirect()
            ->route('superadmin.bagian.index')
            ->with('sukses', 'Bagian / Divisi baru berhasil ditambahkan.');
    }

    public function update(Request $request, Bagian $bagian): RedirectResponse
    {
        $validated = $request->validate([
            'cabang_id' => ['nullable', 'exists:cabang,id'],
            'kode' => ['required', 'string', 'max:20', 'unique:bagian,kode,' . $bagian->id],
            'nama' => ['required', 'string', 'max:255'],
            'retensi_tahun' => ['nullable', 'integer', 'min:1', 'max:100'],
            'retensi_satuan' => ['nullable', 'in:tahun,bulan'],
        ]);

        $validated['kode'] = strtoupper($validated['kode']);

        $bagian->update($validated);

        return redirect()
            ->route('superadmin.bagian.index')
            ->with('sukses', 'Data Bagian / Divisi berhasil diperbarui.');
    }

    public function destroy(Bagian $bagian): RedirectResponse
    {
        if ($bagian->kodeSurat()->exists() || $bagian->dokumen()->exists()) {
            return redirect()
                ->route('superadmin.bagian.index')
                ->with('galat', 'Bagian tidak dapat dihapus karena masih memiliki relasi data Kode Surat atau Dokumen.');
        }

        $bagian->delete();

        return redirect()
            ->route('superadmin.bagian.index')
            ->with('sukses', 'Bagian / Divisi berhasil dihapus.');
    }
}