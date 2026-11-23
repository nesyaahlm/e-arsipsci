<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cabang;
use App\Models\Portofolio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PortofolioController extends Controller
{
    public function index(Request $request): View
    {
        $cari = trim((string) $request->input('cari'));
        $userCabangId = Auth::user()->cabang_id ?? Cabang::where('kode', 'CLC')->value('id') ?? 1;

        $daftarPortofolio = Portofolio::query()
            ->where(function ($q) use ($userCabangId) {
                $q->whereNull('cabang_id')
                    ->orWhere('cabang_id', $userCabangId);
            })
            ->when($cari !== '', function ($query) use ($cari) {
                $query->where(function ($q) use ($cari) {
                    $q->where('kode', 'like', "%{$cari}%")
                      ->orWhere('nama', 'like', "%{$cari}%");
                });
            })
            ->orderByRaw('CASE WHEN cabang_id IS NULL THEN 0 ELSE 1 END')
            ->orderBy('kode')
            ->paginate(15)
            ->withQueryString();

        return view('portofolio.index', compact('daftarPortofolio', 'cari'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge(['kode' => strtoupper(trim((string) $request->input('kode')))]);

        $data = $request->validateWithBag('portofolio', $this->aturan(), $this->pesan());
        $data['cabang_id'] = Auth::user()->cabang_id ?? Cabang::where('kode', 'CLC')->value('id') ?? 1;
        $data['retensi_satuan'] = $data['retensi_satuan'] ?? 'bulan';

        Portofolio::create($data);

        return redirect()
            ->route('portofolio.index')
            ->with('sukses', 'Portofolio sampel cabang berhasil ditambahkan.');
    }

    public function update(Request $request, Portofolio $portofolio): RedirectResponse
    {
        if (is_null($portofolio->cabang_id)) {
            return redirect()
                ->route('portofolio.index')
                ->with('galat', 'Portofolio standar nasional tidak dapat diubah oleh admin cabang.');
        }

        $request->merge(['kode' => strtoupper(trim((string) $request->input('kode')))]);

        $data = $request->validateWithBag('portofolio', $this->aturan($portofolio->id), $this->pesan());
        $data['retensi_satuan'] = $data['retensi_satuan'] ?? 'bulan';

        $portofolio->update($data);

        return redirect()
            ->route('portofolio.index')
            ->with('sukses', 'Portofolio berhasil diperbarui.');
    }

    public function destroy(Portofolio $portofolio): RedirectResponse
    {
        if (is_null($portofolio->cabang_id)) {
            return redirect()
                ->route('portofolio.index')
                ->with('galat', 'Portofolio standar nasional tidak dapat dihapus oleh admin cabang.');
        }

        if ($portofolio->sampel()->exists()) {
            return redirect()
                ->route('portofolio.index')
                ->with('galat', 'Portofolio tidak dapat dihapus karena masih memiliki relasi data Sampel.');
        }

        $portofolio->delete();

        return redirect()
            ->route('portofolio.index')
            ->with('sukses', 'Portofolio berhasil dihapus.');
    }

    private function aturan(?int $abaikanId = null): array
    {
        return [
            'kode' => ['required', 'string', 'max:10', Rule::unique('portofolio', 'kode')->ignore($abaikanId)],
            'nama' => ['required', 'string', 'max:255', Rule::unique('portofolio', 'nama')->ignore($abaikanId)],
            'retensi_tahun' => ['nullable', 'integer', 'min:1', 'max:100'],
            'retensi_satuan' => ['nullable', 'in:tahun,bulan'],
        ];
    }

    private function pesan(): array
    {
        return [
            'kode.required' => 'Kode portofolio wajib diisi.',
            'kode.max' => 'Kode portofolio maksimal 10 karakter.',
            'kode.unique' => 'Kode portofolio sudah dipakai.',
            'nama.required' => 'Nama portofolio wajib diisi.',
            'nama.max' => 'Nama portofolio maksimal 255 karakter.',
            'nama.unique' => 'Nama portofolio sudah dipakai.',
            'retensi_tahun.integer' => 'Retensi harus berupa angka.',
            'retensi_tahun.min' => 'Retensi minimal 1.',
            'retensi_tahun.max' => 'Retensi maksimal 100.',
        ];
    }
}