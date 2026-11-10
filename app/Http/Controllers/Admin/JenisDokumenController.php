<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisDokumen;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JenisDokumenController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->merge(['kode' => strtoupper((string) $request->input('kode'))]);

        $data = $request->validateWithBag('jenis', $this->aturan(), $this->pesan());

        $jenis = JenisDokumen::create([
            'kode' => $data['kode'],
            'nama' => $data['nama'],
            'retensi_tahun' => $data['retensi_tahun'] ?? null,
        ]);

        $jenis->layanan()->sync($data['layanan_ids']);

        return redirect()
            ->route('layanan.index')
            ->with('sukses', 'Jenis dokumen berhasil ditambahkan.')
            ->with('tab', 'jenis');
    }

    public function update(Request $request, JenisDokumen $jenis): RedirectResponse
    {
        $request->merge(['kode' => strtoupper((string) $request->input('kode'))]);

        $data = $request->validateWithBag('jenis', $this->aturan($jenis->id), $this->pesan());

        $jenis->update([
            'kode' => $data['kode'],
            'nama' => $data['nama'],
            'retensi_tahun' => $data['retensi_tahun'] ?? null,
        ]);

        $jenis->layanan()->sync($data['layanan_ids']);

        return redirect()
            ->route('layanan.index')
            ->with('sukses', 'Jenis dokumen berhasil diperbarui.')
            ->with('tab', 'jenis');
    }

    public function destroy(JenisDokumen $jenis): RedirectResponse
    {
        if ($jenis->dokumen()->exists()) {
            return redirect()
                ->route('layanan.index')
                ->with('galat', "Jenis dokumen {$jenis->nama} tidak bisa dihapus karena masih dipakai oleh dokumen.")
                ->with('tab', 'jenis');
        }

        $jenis->layanan()->detach();
        $jenis->delete();

        return redirect()
            ->route('layanan.index')
            ->with('sukses', 'Jenis dokumen berhasil dihapus.')
            ->with('tab', 'jenis');
    }

    private function aturan(?int $abaikanId = null): array
    {
        return [
            'kode' => ['required', 'alpha_num', 'max:10', Rule::unique('jenis_dokumen', 'kode')->ignore($abaikanId)],
            'nama' => ['required', 'string', 'max:255'],
            'retensi_tahun' => ['nullable', 'integer', 'min:1', 'max:100'],
            'layanan_ids' => ['required', 'array', 'min:1'],
            'layanan_ids.*' => ['integer', 'exists:layanan,id'],
        ];
    }

    private function pesan(): array
    {
        return [
            'kode.required' => 'Kode jenis wajib diisi.',
            'kode.alpha_num' => 'Kode jenis hanya boleh huruf dan angka.',
            'kode.max' => 'Kode jenis maksimal 10 karakter.',
            'kode.unique' => 'Kode jenis sudah dipakai.',
            'nama.required' => 'Nama jenis wajib diisi.',
            'nama.max' => 'Nama jenis maksimal 255 karakter.',
            'retensi_tahun.integer' => 'Retensi harus berupa angka (tahun).',
            'retensi_tahun.min' => 'Retensi minimal 1 tahun.',
            'retensi_tahun.max' => 'Retensi maksimal 100 tahun.',
            'layanan_ids.required' => 'Pilih minimal satu layanan.',
            'layanan_ids.min' => 'Pilih minimal satu layanan.',
        ];
    }
}