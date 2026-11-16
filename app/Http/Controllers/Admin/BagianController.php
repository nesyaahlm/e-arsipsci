<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bagian;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BagianController extends Controller
{
    public function index(): View
    {
        return view('bagian.index', [
            'daftarBagian' => Bagian::orderBy('kode')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge(['kode' => strtoupper(trim((string) $request->input('kode')))]);

        $data = $request->validateWithBag('bagian', $this->aturan(), $this->pesan());

        Bagian::create($data);

        return redirect()
            ->route('bagian.index')
            ->with('sukses', 'Bagian berhasil ditambahkan.');
    }

    public function update(Request $request, Bagian $bagian): RedirectResponse
    {
        $request->merge(['kode' => strtoupper(trim((string) $request->input('kode')))]);

        $data = $request->validateWithBag('bagian', $this->aturan($bagian->id), $this->pesan());

        $bagian->update($data);

        return redirect()
            ->route('bagian.index')
            ->with('sukses', 'Bagian berhasil diperbarui.');
    }

    public function destroy(Bagian $bagian): RedirectResponse
    {
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
            'retensi_tahun.min' => 'Retensi minimal 1 tahun.',
            'retensi_tahun.max' => 'Retensi maksimal 100 tahun.',
        ];
    }
}