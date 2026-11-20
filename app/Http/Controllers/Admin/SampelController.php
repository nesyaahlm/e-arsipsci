<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bagian;
use App\Models\Cabang;
use App\Models\Sampel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SampelController extends Controller
{
    public function index(Request $request): View
    {
        $daftarSampel = Sampel::with('bagian')->where('status', '!=', 'pemusnahan')->get();
        foreach ($daftarSampel as $s) {
            if ($s->tanggal_masuk_arsip && $s->bagian && $s->bagian->retensi_tahun) {
                if (($s->bagian->retensi_satuan ?? 'tahun') === 'bulan') {
                    $kadaluwarsa = Carbon::parse($s->tanggal_masuk_arsip)->addMonths($s->bagian->retensi_tahun);
                } else {
                    $kadaluwarsa = Carbon::parse($s->tanggal_masuk_arsip)->addYears($s->bagian->retensi_tahun);
                }

                if ($kadaluwarsa->isPast() && $s->status !== 'inaktif') {
                    $s->update(['status' => 'inaktif']);
                } elseif (!$kadaluwarsa->isPast() && $s->status === 'inaktif') {
                    $s->update(['status' => 'aktif']);
                }
            }
        }

        $cari = trim((string) $request->input('cari'));
        $userCabangId = Auth::user()->cabang_id ?? Cabang::where('kode', 'CLC')->value('id') ?? 1;

        $sampel = Sampel::with(['bagian', 'pengunggah'])
            ->when($cari !== '', function ($query) use ($cari) {
                $query->where(function ($q) use ($cari) {
                    $kolom = [
                        'kode_sampel',
                        'nama_sampel',
                        'shipper',
                        'vessel',
                        'bill_of_lading',
                        'tempat_pengambilan',
                        'analysis_for',
                        'nomor_ruang_arsip',
                        'nama_ruang_arsip',
                        'deskripsi',
                        'tanggal_pengambilan',
                        'tanggal_masuk_arsip',
                    ];

                    foreach ($kolom as $nama) {
                        $q->orWhere($nama, 'like', "%{$cari}%");
                    }

                    $q->orWhereHas('bagian', function ($b) use ($cari) {
                        $b->where('nama', 'like', "%{$cari}%");
                    })->orWhereHas('pengunggah', function ($u) use ($cari) {
                        $u->where('name', 'like', "%{$cari}%");
                    });
                });
            })
            ->when($request->filled('bagian_id'), function ($query) use ($request) {
                $query->where('bagian_id', $request->input('bagian_id'));
            })
            ->orderByDesc('tanggal_masuk_arsip')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $daftarBagian = Bagian::where(function ($q) use ($userCabangId) {
            $q->whereNull('cabang_id')
                ->orWhere('cabang_id', $userCabangId);
        })->orderBy('nama')->get();

        return view('admin.sampel.index', [
            'sampel' => $sampel,
            'daftarBagian' => $daftarBagian,
        ]);
    }

    public function show(Sampel $sampel): View
    {
        $sampel->load(['bagian', 'pengunggah']);

        DB::table('log_aktivitas')->insert([
            'user_id' => Auth::id(),
            'aktivitas' => 'melihat_sampel',
            'dokumen_id' => null,
            'sampel_id' => $sampel->id,
            'keterangan' => $sampel->nama_sampel,
            'created_at' => now(),
        ]);

        return view('admin.sampel.show', ['sampel' => $sampel]);
    }

    public function create(): View
    {
        $userCabangId = Auth::user()->cabang_id ?? Cabang::where('kode', 'CLC')->value('id') ?? 1;

        $bagianList = Bagian::where(function ($q) use ($userCabangId) {
            $q->whereNull('cabang_id')
                ->orWhere('cabang_id', $userCabangId);
        })->orderBy('nama')->get();

        return view('admin.sampel.create', compact('bagianList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->aturan(), $this->pesan('Kode sampel sudah terdaftar, gunakan kode lain.'));

        $data = collect($validated)->except('foto')->all();
        $data['tahun_arsip'] = Sampel::rapikanTahun($data['tahun_arsip'] ?? null);
        $data['foto_path'] = $request->hasFile('foto')
            ? $request->file('foto')->store('sampel', 'public')
            : null;

        $data['cabang_id'] = Auth::user()->cabang_id ?? Cabang::where('kode', 'CLC')->value('id') ?? 1;
        $data['diunggah_oleh'] = Auth::id();

        $bagian = Bagian::find($data['bagian_id']);
        if ($bagian && !empty($bagian->retensi_tahun) && !empty($data['tanggal_masuk_arsip'])) {
            $tgl = Carbon::parse($data['tanggal_masuk_arsip']);
            $kadaluwarsa = (($bagian->retensi_satuan ?? 'tahun') === 'bulan')
                ? $tgl->addMonths($bagian->retensi_tahun)
                : $tgl->addYears($bagian->retensi_tahun);

            $data['status'] = $kadaluwarsa->isPast() ? 'inaktif' : 'aktif';
        }

        $sampel = Sampel::create($data);

        DB::table('log_aktivitas')->insert([
            'user_id' => Auth::id(),
            'aktivitas' => 'mengunggah_sampel',
            'dokumen_id' => null,
            'sampel_id' => $sampel->id,
            'keterangan' => 'Menambahkan sampel ' . $sampel->nama_sampel,
            'created_at' => now(),
        ]);

        return redirect()
            ->route('admin.sampel.index')
            ->with('success', 'Data sampel ' . $sampel->kode_sampel . ' berhasil disimpan!');
    }

    public function edit($id): View
    {
        $sampel = Sampel::findOrFail($id);
        $userCabangId = Auth::user()->cabang_id ?? Cabang::where('kode', 'CLC')->value('id') ?? 1;

        $bagianList = Bagian::where(function ($q) use ($userCabangId) {
            $q->whereNull('cabang_id')
                ->orWhere('cabang_id', $userCabangId);
        })->orderBy('nama')->get();

        return view('admin.sampel.edit', compact('sampel', 'bagianList'));
    }

    public function cetakLabel($id): View
    {
        $sampel = Sampel::with(['bagian', 'pengunggah'])->findOrFail($id);

        DB::table('log_aktivitas')->insert([
            'user_id' => Auth::id(),
            'aktivitas' => 'mencetak_label_sampel',
            'dokumen_id' => null,
            'sampel_id' => $sampel->id,
            'keterangan' => 'Mencetak label sampel ' . $sampel->nama_sampel,
            'created_at' => now(),
        ]);

        return view('admin.sampel.cetak-label', compact('sampel'));
    }

    public function update(Request $request, $id)
    {
        $sampel = Sampel::findOrFail($id);

        $validated = $request->validate($this->aturan((int) $id), $this->pesan('Kode sampel sudah digunakan sampel lain.'));

        $data = collect($validated)->except('foto')->all();
        $data['tahun_arsip'] = Sampel::rapikanTahun($data['tahun_arsip'] ?? null);
        $data['foto_path'] = $sampel->foto_path;

        if ($request->hasFile('foto')) {
            if ($sampel->foto_path && Storage::disk('public')->exists($sampel->foto_path)) {
                Storage::disk('public')->delete($sampel->foto_path);
            }
            $data['foto_path'] = $request->file('foto')->store('sampel', 'public');
        }

        $bagian = Bagian::find($data['bagian_id']);
        if ($bagian && !empty($bagian->retensi_tahun) && !empty($data['tanggal_masuk_arsip'])) {
            $tgl = Carbon::parse($data['tanggal_masuk_arsip']);
            $kadaluwarsa = (($bagian->retensi_satuan ?? 'tahun') === 'bulan')
                ? $tgl->addMonths($bagian->retensi_tahun)
                : $tgl->addYears($bagian->retensi_tahun);

            if ($sampel->status !== 'pemusnahan') {
                $data['status'] = $kadaluwarsa->isPast() ? 'inaktif' : 'aktif';
            }
        }

        $sampel->update($data);

        DB::table('log_aktivitas')->insert([
            'user_id' => Auth::id(),
            'aktivitas' => 'mengubah_sampel',
            'dokumen_id' => null,
            'sampel_id' => $sampel->id,
            'keterangan' => 'Memperbarui data sampel ' . $sampel->nama_sampel,
            'created_at' => now(),
        ]);

        return redirect()
            ->route('admin.sampel.index')
            ->with('success', 'Data sampel ' . $sampel->kode_sampel . ' berhasil diperbarui!');
    }

    public function destroy(Sampel $sampel)
    {
        DB::table('log_aktivitas')->insert([
            'user_id' => Auth::id(),
            'aktivitas' => 'menghapus_sampel',
            'dokumen_id' => null,
            'sampel_id' => $sampel->id,
            'keterangan' => 'Menghapus sampel ' . $sampel->nama_sampel,
            'created_at' => now(),
        ]);

        $kode = $sampel->kode_sampel;
        $sampel->delete();

        return redirect()
            ->route('admin.sampel.index')
            ->with('success', 'Sampel ' . $kode . ' berhasil dihapus.');
    }

    private function aturan(?int $abaikanId = null): array
    {
        return [
            'kode_sampel' => ['required', 'string', 'max:50', 'unique:sampel,kode_sampel' . ($abaikanId ? ',' . $abaikanId : '')],
            'nama_sampel' => ['required', 'string', 'max:255'],
            'shipper' => ['nullable', 'string', 'max:255'],
            'vessel' => ['nullable', 'string', 'max:255'],
            'bill_of_lading' => ['nullable', 'string', 'max:255'],
            'tanggal_pengambilan' => ['required', 'date'],
            'tempat_pengambilan' => ['required', 'string', 'max:255'],
            'analysis_for' => ['required', 'string', 'max:255'],
            'bagian_id' => ['required', 'exists:bagian,id'],
            'nomor_ruang_arsip' => ['required', 'string', 'max:50'],
            'nomor_tingkat_arsip' => ['nullable', 'string', 'max:50'],
            'tahun_arsip' => Sampel::aturanTahun(),
            'nama_ruang_arsip' => ['required', 'string', 'max:100'],
            'tanggal_masuk_arsip' => ['required', 'date'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:5120'],
            'deskripsi' => ['nullable', 'string'],
        ];
    }

    private function pesan(string $pesanUnik): array
    {
        return [
            'kode_sampel.required' => 'Kode sampel wajib diisi.',
            'kode_sampel.unique' => $pesanUnik,
            'nama_sampel.required' => 'Nama sampel / komoditas wajib diisi.',
            'tanggal_pengambilan.required' => 'Tanggal pengambilan sampel wajib diisi.',
            'tempat_pengambilan.required' => 'Tempat pengambilan sampel wajib diisi.',
            'analysis_for.required' => 'Tujuan analisis wajib diisi.',
            'bagian_id.required' => 'Bagian wajib dipilih.',
            'bagian_id.exists' => 'Bagian yang dipilih tidak valid.',
            'nomor_ruang_arsip.required' => 'Nomor rak arsip wajib diisi.',
            'nama_ruang_arsip.required' => 'Nama ruang arsip sampel wajib diisi.',
            'tanggal_masuk_arsip.required' => 'Tanggal masuk arsip wajib diisi.',
            'foto.image' => 'Berkas harus berupa gambar.',
            'foto.mimes' => 'Format foto harus berupa JPG, JPEG, atau PNG.',
            'foto.max' => 'Ukuran foto maksimal 5 MB.',
        ];
    }
}