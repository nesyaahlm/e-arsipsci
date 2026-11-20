<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bagian;
use App\Models\Sampel;
use App\Support\RekapExcel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SampelController extends Controller
{
    public function index(Request $request): View
    {
        $sampel = $this->queryFilter($request)
            ->paginate(10)
            ->withQueryString();

        return view('admin.sampel.index', [
            'sampel' => $sampel,
            'daftarBagian' => Bagian::orderBy('nama')->get(),
        ]);
    }

    public function export(Request $request)
    {
        $daftar = $this->queryFilter($request)->get();

        $kolom = [
            ['judul' => 'Kode Sampel', 'lebar' => 18],
            ['judul' => 'Nama Sampel', 'lebar' => 30],
            ['judul' => 'Bagian', 'lebar' => 22],
            ['judul' => 'Status', 'lebar' => 14],
            ['judul' => 'Shipper', 'lebar' => 24],
            ['judul' => 'Vessel', 'lebar' => 22],
            ['judul' => 'Bill of Lading', 'lebar' => 20],
            ['judul' => 'Tanggal Pengambilan', 'lebar' => 20],
            ['judul' => 'Tahun Arsip', 'lebar' => 16],
            ['judul' => 'Tempat Pengambilan', 'lebar' => 26],
            ['judul' => 'Analysis For', 'lebar' => 22],
            ['judul' => 'Tanggal Masuk Arsip', 'lebar' => 20],
            ['judul' => 'No. Rak', 'lebar' => 12],
            ['judul' => 'No. Tingkat', 'lebar' => 12],
            ['judul' => 'Ruang Arsip', 'lebar' => 22],
            ['judul' => 'Diunggah Oleh', 'lebar' => 22],
            ['judul' => 'Deskripsi', 'lebar' => 38],
        ];

        $baris = $daftar->map(function ($s) {
            return [
                $s->kode_sampel,
                $s->nama_sampel,
                $s->bagian->nama ?? '-',
                ucfirst((string) $s->status),
                $s->shipper ?: '-',
                $s->vessel ?: '-',
                $s->bill_of_lading ?: '-',
                $s->tanggal_pengambilan?->format('d/m/Y') ?? '-',
                $s->label_tahun,
                $s->tempat_pengambilan ?: '-',
                $s->analysis_for ?: '-',
                $s->tanggal_masuk_arsip?->format('d/m/Y') ?? '-',
                $s->nomor_ruang_arsip ?: '-',
                $s->nomor_tingkat_arsip ?: '-',
                $s->nama_ruang_arsip ?: '-',
                $s->pengunggah->name ?? '-',
                $s->deskripsi ?: '-',
            ];
        });

        $keterangan = 'Diunduh: ' . now('Asia/Jakarta')->format('d/m/Y H:i')
            . ' | Filter: ' . $this->keteranganFilter($request)
            . ' | Total: ' . $daftar->count() . ' sampel';

        return RekapExcel::unduh(
            'rekap-sampel-' . now('Asia/Jakarta')->format('Ymd-His') . '.xlsx',
            'Rekap Sampel',
            'Rekap Arsip Sampel',
            $keterangan,
            $kolom,
            $baris
        );
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
        $bagianList = Bagian::orderBy('nama')->get();

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
        $data['diunggah_oleh'] = Auth::id();

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
        $bagianList = Bagian::orderBy('nama')->get();

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

    private function queryFilter(Request $request)
    {
        $cari = trim((string) $request->input('cari'));

        return Sampel::with(['bagian', 'pengunggah'])
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
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->orderByDesc('tanggal_masuk_arsip')
            ->orderByDesc('id');
    }

    private function keteranganFilter(Request $request): string
    {
        $filter = [];

        if (trim((string) $request->input('cari')) !== '') {
            $filter[] = 'Cari "' . trim((string) $request->input('cari')) . '"';
        }
        if ($request->filled('bagian_id')) {
            $filter[] = 'Bagian ' . (Bagian::find($request->input('bagian_id'))?->nama ?? '-');
        }
        if ($request->filled('status')) {
            $filter[] = 'Status ' . ucfirst((string) $request->input('status'));
        }

        return $filter ? implode(', ', $filter) : 'Semua data';
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