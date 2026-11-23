<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Cabang;
use App\Models\Portofolio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PortofolioController extends Controller
{
    public function index(Request $request): View
    {
        $cari = $request->query('cari');
        $cabangId = $request->query('cabang_id', 'nasional');

        $query = Portofolio::with(['cabang'])
            ->withCount(['sampel']);

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

        $daftarPortofolio = $query->orderBy('nama', 'asc')
            ->paginate(10)
            ->withQueryString();

        $daftarCabang = Cabang::orderBy('nama', 'asc')->get();

        return view('superadmin.portofolio.index', compact('daftarPortofolio', 'daftarCabang', 'cari', 'cabangId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'cabang_id' => ['nullable', 'exists:cabang,id'],
            'kode' => ['required', 'string', 'max:20', 'unique:portofolio,kode'],
            'nama' => ['required', 'string', 'max:255'],
            'retensi_tahun' => ['nullable', 'integer', 'min:1', 'max:100'],
            'retensi_satuan' => ['nullable', 'in:tahun,bulan'],
        ]);

        $validated['kode'] = strtoupper($validated['kode']);
        $validated['retensi_satuan'] = $validated['retensi_satuan'] ?? 'bulan';

        Portofolio::create($validated);

        return redirect()
            ->route('superadmin.portofolio.index')
            ->with('sukses', 'Portofolio komoditas sampel baru berhasil ditambahkan.');
    }

    public function update(Request $request, Portofolio $portofolio): RedirectResponse
    {
        $validated = $request->validate([
            'cabang_id' => ['nullable', 'exists:cabang,id'],
            'kode' => ['required', 'string', 'max:20', 'unique:portofolio,kode,' . $portofolio->id],
            'nama' => ['required', 'string', 'max:255'],
            'retensi_tahun' => ['nullable', 'integer', 'min:1', 'max:100'],
            'retensi_satuan' => ['nullable', 'in:tahun,bulan'],
        ]);

        $validated['kode'] = strtoupper($validated['kode']);
        $validated['retensi_satuan'] = $validated['retensi_satuan'] ?? 'bulan';

        $portofolio->update($validated);

        return redirect()
            ->route('superadmin.portofolio.index')
            ->with('sukses', 'Data Portofolio berhasil diperbarui.');
    }

    public function destroy(Portofolio $portofolio): RedirectResponse
    {
        if ($portofolio->sampel()->exists()) {
            return redirect()
                ->route('superadmin.portofolio.index')
                ->with('galat', 'Portofolio tidak dapat dihapus karena masih memiliki relasi data Sampel.');
        }

        $portofolio->delete();

        return redirect()
            ->route('superadmin.portofolio.index')
            ->with('sukses', 'Portofolio berhasil dihapus.');
    }
}