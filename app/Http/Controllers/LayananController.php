<?php

namespace App\Http\Controllers;

use App\Models\JenisDokumen;
use App\Models\Layanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LayananController extends Controller
{
    public function index(): View
    {
        return view('layanan.index', [
            'daftarLayanan' => Layanan::with('jenisDokumen')->orderBy('id')->get(),
            'daftarJenis' => JenisDokumen::with('layanan')->orderBy('kode')->get(),
            'bisaKelola' => (bool) auth()->user()?->isAdmin(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validateWithBag('tambah', [
            'nama' => ['required', 'string', 'max:255', 'unique:layanan,nama'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
        ], $this->pesan());

        Layanan::create($data);

        return redirect()
            ->route('layanan.index')
            ->with('sukses', 'Layanan berhasil ditambahkan.');
    }

    public function update(Request $request, Layanan $layanan): RedirectResponse
    {
        $data = $request->validateWithBag('edit', [
            'nama' => ['required', 'string', 'max:255', Rule::unique('layanan', 'nama')->ignore($layanan->id)],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
        ], $this->pesan());

        $layanan->update($data);

        return redirect()
            ->route('layanan.index')
            ->with('sukses', 'Layanan berhasil diperbarui.');
    }

    public function destroy(Layanan $layanan): RedirectResponse
    {
        if ($layanan->dokumen()->exists() || $layanan->jenisDokumen()->exists()) {
            return redirect()
                ->route('layanan.index')
                ->with('galat', "Layanan {$layanan->nama} tidak bisa dihapus karena masih memiliki jenis dokumen atau dokumen.");
        }

        $layanan->delete();

        return redirect()
            ->route('layanan.index')
            ->with('sukses', 'Layanan berhasil dihapus.');
    }

    private function pesan(): array
    {
        return [
            'nama.required' => 'Nama layanan wajib diisi.',
            'nama.unique' => 'Nama layanan sudah dipakai.',
            'nama.max' => 'Nama layanan maksimal 255 karakter.',
            'deskripsi.max' => 'Deskripsi maksimal 1000 karakter.',
        ];
    }
}