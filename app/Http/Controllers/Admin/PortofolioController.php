<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cabang;
use App\Models\Portofolio;
use App\Models\KodeSurat;
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

        $daftarPortofolio = Portofolio::with(['kodeSurat' => function ($q) use ($userCabangId) {
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

    public function storeKlasifikasi(Request $request, Portofolio $portofolio): RedirectResponse
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
            'kode.required' => 'Kode sampel wajib diisi.',
            'kode.unique' => 'Kode sampel sudah digunakan di sistem.',
            'kode.max' => 'Kode sampel maksimal 20 karakter.',
            'nama.required' => 'Nama klasifikasi sampel wajib diisi.',
            'nama.max' => 'Nama klasifikasi sampel maksimal 255 karakter.',
            'retensi_aktif.required' => 'Masa retensi aktif wajib diisi.',
            'retensi_aktif.integer' => 'Masa retensi aktif harus berupa angka.',
            'retensi_inaktif.required' => 'Masa retensi inaktif wajib diisi.',
            'retensi_inaktif.integer' => 'Masa retensi inaktif harus berupa angka.',
        ]);

        KodeSurat::create([
            'bagian_id' => null,
            'portofolio_id' => $portofolio->id,
            'cabang_id' => $userCabangId,
            'kode' => $data['kode'],
            'nama' => $data['nama'],
            'retensi_aktif' => $data['retensi_aktif'],
            'retensi_inaktif' => $data['retensi_inaktif'],
            'keterangan' => $data['keterangan'] ?? null,
        ]);

        return redirect()
            ->route('portofolio.index')
            ->with('sukses', 'Klasifikasi sampel ' . $data['kode'] . ' berhasil ditambahkan ke portofolio ' . $portofolio->nama . '.');
    }

    public function destroyKlasifikasi(KodeSurat $kodeSurat): RedirectResponse
    {
        $userCabangId = Auth::user()->cabang_id ?? Cabang::where('kode', 'CLC')->value('id') ?? 1;

        if (is_null($kodeSurat->cabang_id)) {
            return redirect()
                ->route('portofolio.index')
                ->with('galat', 'Klasifikasi standar nasional tidak dapat dihapus oleh Admin Cabang.');
        }

        if ((int) $kodeSurat->cabang_id !== (int) $userCabangId) {
            return redirect()
                ->route('portofolio.index')
                ->with('galat', 'Anda tidak memiliki hak akses untuk menghapus klasifikasi cabang lain.');
        }

        $kode = $kodeSurat->kode;
        $kodeSurat->delete();

        return redirect()
            ->route('portofolio.index')
            ->with('sukses', 'Klasifikasi sampel ' . $kode . ' berhasil dihapus.');
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