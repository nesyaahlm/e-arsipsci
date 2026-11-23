<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bagian;
use App\Models\Cabang;
use App\Models\KodeSurat;
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

        $daftarBagian = Bagian::with(['kodeSurat' => function ($q) use ($userCabangId) {
                $q->whereNull('cabang_id')
                  ->orWhere('cabang_id', $userCabangId);
            }])
            ->where(function ($q) use ($userCabangId) {
                $q->whereNull('cabang_id')
                    ->orWhere('cabang_id', $userCabangId);
            })
            ->when($cari !== '', function ($query) use ($cari) {
                $query->where(function ($q) use ($cari) {
                    $q->where('kode', 'like', "%{$cari}%")
                      ->orWhere('nama', 'like', "%{$cari}%")
                      ->orWhere('retensi_tahun', 'like', "%{$cari}%")
                      ->orWhereHas('kodeSurat', function ($ks) use ($cari) {
                          $ks->where('kode', 'like', "%{$cari}%")
                             ->orWhere('nama', 'like', "%{$cari}%");
                      });
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

    public function storeKlasifikasi(Request $request, Bagian $bagian): RedirectResponse
    {
        $userCabangId = Auth::user()->cabang_id ?? Cabang::where('kode', 'CLC')->value('id') ?? 1;

        $request->merge(['kode' => strtoupper(trim((string) $request->input('kode')))]);

        $data = $request->validate([
            'kode' => ['required', 'string', 'max:20', 'unique:kode_surat,kode'],
            'nama' => ['required', 'string', 'max:255'],
            'retensi_aktif' => ['required', 'integer', 'min:1', 'max:50'],
            'retensi_inaktif' => ['required', 'integer', 'min:0', 'max:50'],
            'keterangan' => ['nullable', 'string'],
        ], [
            'kode.required' => 'Kode surat wajib diisi.',
            'kode.unique' => 'Kode surat sudah digunakan di sistem.',
            'kode.max' => 'Kode surat maksimal 20 karakter.',
            'nama.required' => 'Nama klasifikasi surat wajib diisi.',
            'nama.max' => 'Nama klasifikasi surat maksimal 255 karakter.',
            'retensi_aktif.required' => 'Masa retensi aktif wajib diisi.',
            'retensi_aktif.integer' => 'Masa retensi aktif harus berupa angka.',
            'retensi_inaktif.required' => 'Masa retensi inaktif wajib diisi.',
            'retensi_inaktif.integer' => 'Masa retensi inaktif harus berupa angka.',
        ]);

        KodeSurat::create([
            'bagian_id' => $bagian->id,
            'portofolio_id' => null,
            'cabang_id' => $userCabangId,
            'kode' => $data['kode'],
            'nama' => $data['nama'],
            'retensi_aktif' => $data['retensi_aktif'],
            'retensi_inaktif' => $data['retensi_inaktif'],
            'keterangan' => $data['keterangan'] ?? null,
        ]);

        return redirect()
            ->route('bagian.index')
            ->with('sukses', 'Klasifikasi surat ' . $data['kode'] . ' berhasil ditambahkan ke bagian ' . $bagian->nama . '.');
    }

    public function destroyKlasifikasi(KodeSurat $kodeSurat): RedirectResponse
    {
        $userCabangId = Auth::user()->cabang_id ?? Cabang::where('kode', 'CLC')->value('id') ?? 1;

        if (is_null($kodeSurat->cabang_id)) {
            return redirect()
                ->route('bagian.index')
                ->with('galat', 'Klasifikasi standar nasional tidak dapat dihapus oleh Admin Cabang.');
        }

        if ((int) $kodeSurat->cabang_id !== (int) $userCabangId) {
            return redirect()
                ->route('bagian.index')
                ->with('galat', 'Anda tidak memiliki hak akses untuk menghapus klasifikasi cabang lain.');
        }

        $kode = $kodeSurat->kode;
        $kodeSurat->delete();

        return redirect()
            ->route('bagian.index')
            ->with('sukses', 'Klasifikasi surat ' . $kode . ' berhasil dihapus.');
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