<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bagian;
use App\Models\Cabang;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BagianController extends Controller
{
    public function index(Request $request): View
    {
        $cari = trim((string) $request->input('cari'));
        $userCabangId = Auth::user()->cabang_id ?? Cabang::where('kode', 'CLC')->value('id') ?? 1;

        $daftarBagian = Bagian::query()
            ->where(function ($q) use ($userCabangId) {
                $q->whereNull('cabang_id')
                    ->orWhere('cabang_id', $userCabangId);
            })
            ->when($cari !== '', function ($query) use ($cari) {
                $query->where(function ($q) use ($cari) {
                    $q->where('kode', 'like', "%{$cari}%")
                      ->orWhere('nama', 'like', "%{$cari}%")
                      ->orWhere('retensi_tahun', 'like', "%{$cari}%");
                });
            })
            ->orderByRaw('CASE WHEN cabang_id IS NULL THEN 0 ELSE 1 END')
            ->orderBy('kode')
            ->paginate(15)
            ->withQueryString();

        return view('bagian.index', compact('daftarBagian', 'cari'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge(['kode' => strtoupper(trim((string) $request->input('kode')))]);

        $data = $request->validateWithBag('bagian', $this->aturan(), $this->pesan());
        $data['cabang_id'] = Auth::user()->cabang_id ?? Cabang::where('kode', 'CLC')->value('id') ?? 1;
        $data['retensi_satuan'] = $data['retensi_satuan'] ?? 'tahun';

        Bagian::create($data);

        return redirect()
            ->route('bagian.index')
            ->with('sukses', 'Bagian cabang berhasil ditambahkan.');
    }

    public function update(Request $request, Bagian $bagian): RedirectResponse
    {
        if (is_null($bagian->cabang_id)) {
            return redirect()
                ->route('bagian.index')
                ->with('galat', 'Bagian standar nasional tidak dapat diubah oleh admin cabang.');
        }

        $request->merge(['kode' => strtoupper(trim((string) $request->input('kode')))]);

        $data = $request->validateWithBag('bagian', $this->aturan($bagian->id), $this->pesan());
        $data['retensi_satuan'] = $data['retensi_satuan'] ?? 'tahun';

        $bagian->update($data);

        return redirect()
            ->route('bagian.index')
            ->with('sukses', 'Bagian berhasil diperbarui.');
    }

    public function destroy(Bagian $bagian): RedirectResponse
    {
        if (is_null($bagian->cabang_id)) {
            return redirect()
                ->route('bagian.index')
                ->with('galat', 'Bagian standar nasional tidak dapat dihapus oleh admin cabang.');
        }

        $bagian->delete();

        return redirect()
            ->route('bagian.index')
            ->with('sukses', 'Bagian berhasil dihapus.');
    }

    private function aturan(?int $abaikanId = null): array
    {
        return [
            'kode' => ['required', 'string', 'max:10', Rule::unique('bagian', 'kode')->ignore($abaikanId)],
            'nama' => ['required', 'string', 'max:255', Rule::unique('bagian', 'nama')->ignore($abaikanId)],
            'retensi_tahun' => ['nullable', 'integer', 'min:1', 'max:100'],
            'retensi_satuan' => ['nullable', 'in:tahun,bulan'],
        ];
    }

    private function pesan(): array
    {
        return [
            'kode.required' => 'Kode bagian wajib diisi.',
            'kode.max' => 'Kode bagian maksimal 10 karakter.',
            'kode.unique' => 'Kode bagian sudah dipakai.',
            'nama.required' => 'Nama bagian wajib diisi.',
            'nama.max' => 'Nama bagian maksimal 255 karakter.',
            'nama.unique' => 'Nama bagian sudah dipakai.',
            'retensi_tahun.integer' => 'Retensi harus berupa angka.',
            'retensi_tahun.min' => 'Retensi minimal 1.',
            'retensi_tahun.max' => 'Retensi maksimal 100.',
        ];
    }
}