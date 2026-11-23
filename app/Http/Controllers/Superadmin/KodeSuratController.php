<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Bagian;
use App\Models\KodeSurat;
use App\Models\Portofolio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KodeSuratController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->input('tab', 'dokumen');
        if (!in_array($tab, ['dokumen', 'sampel'], true)) {
            $tab = 'dokumen';
        }

        $cari = trim((string) $request->input('cari'));
        $bagianId = $request->input('bagian_id');
        $portofolioId = $request->input('portofolio_id');

        $query = KodeSurat::query();

        if ($tab === 'sampel') {
            $query->with('portofolio')->whereNotNull('portofolio_id');
            if ($portofolioId) {
                $query->where('portofolio_id', $portofolioId);
            }
        } else {
            $query->with('bagian')->whereNotNull('bagian_id');
            if ($bagianId) {
                $query->where('bagian_id', $bagianId);
            }
        }

        if ($cari !== '') {
            $query->where(function ($q) use ($cari, $tab) {
                $q->where('kode', 'like', "%{$cari}%")
                  ->orWhere('nama', 'like', "%{$cari}%")
                  ->orWhere('keterangan', 'like', "%{$cari}%");

                if ($tab === 'sampel') {
                    $q->orWhereHas('portofolio', function ($p) use ($cari) {
                        $p->where('nama', 'like', "%{$cari}%")
                          ->orWhere('kode', 'like', "%{$cari}%");
                    });
                } else {
                    $q->orWhereHas('bagian', function ($b) use ($cari) {
                        $b->where('nama', 'like', "%{$cari}%");
                    });
                }
            });
        }

        $daftarKodeSurat = $query->orderBy('kode')
            ->paginate(10)
            ->withQueryString();

        $daftarBagian = Bagian::orderBy('nama')->get();
        $daftarPortofolio = Portofolio::orderBy('nama')->get();

        $jumlahDokumen = KodeSurat::whereNotNull('bagian_id')->count();
        $jumlahSampel = KodeSurat::whereNotNull('portofolio_id')->count();

        return view('superadmin.kode-surat.index', compact(
            'daftarKodeSurat',
            'daftarBagian',
            'daftarPortofolio',
            'cari',
            'tab',
            'bagianId',
            'portofolioId',
            'jumlahDokumen',
            'jumlahSampel'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $tab = $request->input('tab', 'dokumen');
        $request->merge(['kode' => strtoupper(trim((string) $request->input('kode')))]);

        $aturan = [
            'kode' => ['required', 'string', 'max:20', 'unique:kode_surat,kode'],
            'nama' => ['required', 'string', 'max:255'],
            'retensi_aktif' => ['required', 'integer', 'min:1', 'max:50'],
            'retensi_inaktif' => ['required', 'integer', 'min:0', 'max:50'],
            'keterangan' => ['nullable', 'string'],
        ];

        $pesan = [
            'kode.required' => 'Kode wajib diisi.',
            'kode.unique' => 'Kode klasifikasi sudah terdaftar.',
            'nama.required' => 'Nama klasifikasi wajib diisi.',
            'retensi_aktif.required' => 'Masa retensi aktif wajib diisi.',
            'retensi_inaktif.required' => 'Masa retensi inaktif wajib diisi.',
        ];

        if ($tab === 'sampel') {
            $aturan['portofolio_id'] = ['required', 'exists:portofolio,id'];
            $pesan['portofolio_id.required'] = 'Portofolio wajib dipilih.';
            $pesan['portofolio_id.exists'] = 'Portofolio yang dipilih tidak valid.';
        } else {
            $aturan['bagian_id'] = ['required', 'exists:bagian,id'];
            $pesan['bagian_id.required'] = 'Bagian wajib dipilih.';
            $pesan['bagian_id.exists'] = 'Bagian yang dipilih tidak valid.';
        }

        $data = $request->validate($aturan, $pesan);

        if ($tab === 'sampel') {
            $data['bagian_id'] = null;
        } else {
            $data['portofolio_id'] = null;
        }

        KodeSurat::create($data);

        return redirect()
            ->route('superadmin.kode-surat.index', ['tab' => $tab])
            ->with('sukses', 'Klasifikasi kearsipan berhasil ditambahkan.');
    }

    public function update(Request $request, KodeSurat $kodeSurat): RedirectResponse
    {
        $tab = $request->input('tab', 'dokumen');
        $request->merge(['kode' => strtoupper(trim((string) $request->input('kode')))]);

        $aturan = [
            'kode' => ['required', 'string', 'max:20', Rule::unique('kode_surat', 'kode')->ignore($kodeSurat->id)],
            'nama' => ['required', 'string', 'max:255'],
            'retensi_aktif' => ['required', 'integer', 'min:1', 'max:50'],
            'retensi_inaktif' => ['required', 'integer', 'min:0', 'max:50'],
            'keterangan' => ['nullable', 'string'],
        ];

        $pesan = [
            'kode.required' => 'Kode wajib diisi.',
            'kode.unique' => 'Kode klasifikasi sudah terdaftar.',
            'nama.required' => 'Nama klasifikasi wajib diisi.',
            'retensi_aktif.required' => 'Masa retensi aktif wajib diisi.',
            'retensi_inaktif.required' => 'Masa retensi inaktif wajib diisi.',
        ];

        if ($tab === 'sampel') {
            $aturan['portofolio_id'] = ['required', 'exists:portofolio,id'];
            $pesan['portofolio_id.required'] = 'Portofolio wajib dipilih.';
            $pesan['portofolio_id.exists'] = 'Portofolio yang dipilih tidak valid.';
        } else {
            $aturan['bagian_id'] = ['required', 'exists:bagian,id'];
            $pesan['bagian_id.required'] = 'Bagian wajib dipilih.';
            $pesan['bagian_id.exists'] = 'Bagian yang dipilih tidak valid.';
        }

        $data = $request->validate($aturan, $pesan);

        if ($tab === 'sampel') {
            $data['bagian_id'] = null;
        } else {
            $data['portofolio_id'] = null;
        }

        $kodeSurat->update($data);

        return redirect()
            ->route('superadmin.kode-surat.index', ['tab' => $tab])
            ->with('sukses', 'Klasifikasi kearsipan berhasil diperbarui.');
    }

    public function destroy(Request $request, KodeSurat $kodeSurat): RedirectResponse
    {
        $tab = $request->input('tab', $kodeSurat->portofolio_id ? 'sampel' : 'dokumen');
        $kodeSurat->delete();

        return redirect()
            ->route('superadmin.kode-surat.index', ['tab' => $tab])
            ->with('sukses', 'Klasifikasi kearsipan berhasil dihapus.');
    }
}