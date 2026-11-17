<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\LogAktivitas;
use App\Models\Pemusnahan;
use App\Models\PemusnahanItem;
use App\Models\Sampel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PemusnahanController extends Controller
{
    public function index()
    {
        $antrean = PemusnahanItem::with('pengaju')
            ->whereNull('pemusnahan_id')
            ->when(! $this->admin(), function ($q) {
                $q->where('diajukan_oleh', Auth::id());
            })
            ->orderBy('tipe')
            ->latest()
            ->get();

        $riwayat = Pemusnahan::with('items')
            ->withCount('items')
            ->when(! $this->admin(), function ($q) {
                $q->where('dibuat_oleh', Auth::id());
            })
            ->latest('tanggal')
            ->latest('id')
            ->paginate(10, ['*'], 'riwayat')
            ->withQueryString();

        $layout = $this->layout();

        return view('pemusnahan.index', compact('antrean', 'riwayat', 'layout'));
    }

    public function ajukan(Request $request)
    {
        $data = $request->validate([
            'tipe' => ['required', Rule::in(['dokumen', 'sampel'])],
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ], [
            'ids.required' => 'Pilih minimal satu data yang akan dimusnahkan.',
            'ids.min' => 'Pilih minimal satu data yang akan dimusnahkan.',
        ]);

        $tipe = $data['tipe'];
        $kolom = $tipe . '_id';

        $daftar = ($tipe === 'dokumen' ? Dokumen::query() : Sampel::query())
            ->whereIn('id', $data['ids'])
            ->when(! $this->admin(), function ($q) {
                $q->where('diunggah_oleh', Auth::id());
            })
            ->get();

        $ditolak = count($data['ids']) - $daftar->count();
        $masuk = 0;
        $dilewati = 0;

        DB::transaction(function () use ($daftar, $tipe, $kolom, &$masuk, &$dilewati) {
            foreach ($daftar as $arsip) {
                if ($arsip->status === 'pemusnahan' || PemusnahanItem::where($kolom, $arsip->id)->exists()) {
                    $dilewati++;
                    continue;
                }

                $sebelumnya = in_array($arsip->status, ['aktif', 'inaktif'], true) ? $arsip->status : 'aktif';

                PemusnahanItem::create([
                    'tipe' => $tipe,
                    $kolom => $arsip->id,
                    'kode' => $tipe === 'dokumen' ? ($arsip->nomor ?? '-') : $arsip->kode_sampel,
                    'judul' => $tipe === 'dokumen' ? $arsip->judul : $arsip->nama_sampel,
                    'status_sebelumnya' => $sebelumnya,
                    'lokasi_sebelumnya' => $tipe === 'dokumen'
                        ? $this->lokasiDokumen($arsip)
                        : trim($arsip->nomor_ruang_arsip . ' / ' . $arsip->nama_ruang_arsip),
                    'diajukan_oleh' => Auth::id(),
                ]);

                $arsip->newQuery()->whereKey($arsip->id)->update(['status' => 'pemusnahan']);

                $masuk++;
            }
        });

        $pesan = "{$masuk} {$tipe} masuk ke daftar pemusnahan dan statusnya menjadi Pemusnahan.";
        if ($dilewati > 0) {
            $pesan .= " {$dilewati} data sudah berstatus pemusnahan dan dilewati.";
        }
        if ($ditolak > 0) {
            $pesan .= " {$ditolak} data dilewati karena bukan unggahan Anda atau tidak ditemukan.";
        }

        return redirect()->route('pemusnahan.index')->with('success', $pesan);
    }

    public function batalkan(PemusnahanItem $item)
    {
        abort_if($item->pemusnahan_id !== null, 404);
        abort_unless($this->admin() || $item->diajukan_oleh === Auth::id(), 403);

        DB::transaction(function () use ($item) {
            $model = $item->tipe === 'dokumen' ? Dokumen::class : Sampel::class;
            $id = $item->tipe === 'dokumen' ? $item->dokumen_id : $item->sampel_id;

            $model::whereKey($id)->update(['status' => $item->status_sebelumnya]);

            $item->delete();
        });

        return redirect()->route('pemusnahan.index')->with('success', 'Data dikeluarkan dari daftar pemusnahan dan statusnya dikembalikan.');
    }

    public function musnahkan(Request $request)
    {
        $data = $request->validateWithBag('musnah', [
            'mode' => ['required', Rule::in(['terpilih', 'semua'])],
            'ids' => ['required_if:mode,terpilih', 'array'],
            'ids.*' => ['integer'],
            'nomor' => ['required', 'string', 'max:100', 'unique:pemusnahan,nomor'],
            'tanggal' => ['required', 'date', 'before_or_equal:today'],
            'pelaksana' => ['required', 'string', 'max:255'],
            'bukti' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ], [
            'ids.required_if' => 'Pilih minimal satu data yang akan dimusnahkan.',
            'nomor.required' => 'Nomor pemusnahan wajib diisi.',
            'nomor.unique' => 'Nomor pemusnahan sudah dipakai.',
            'tanggal.required' => 'Tanggal pemusnahan wajib diisi.',
            'tanggal.before_or_equal' => 'Tanggal pemusnahan tidak boleh melebihi hari ini.',
            'pelaksana.required' => 'Nama pelaksana pemusnahan wajib diisi.',
            'bukti.required' => 'Bukti pemusnahan wajib diunggah.',
            'bukti.mimes' => 'Bukti pemusnahan harus berupa PDF, JPG, atau PNG.',
            'bukti.max' => 'Ukuran bukti pemusnahan melebihi batas 10 MB.',
        ]);

        $antrean = PemusnahanItem::whereNull('pemusnahan_id')
            ->when(! $this->admin(), function ($q) {
                $q->where('diajukan_oleh', Auth::id());
            })
            ->when($data['mode'] === 'terpilih', function ($q) use ($data) {
                $q->whereIn('id', $data['ids']);
            })
            ->get();

        if ($antrean->isEmpty()) {
            return redirect()->route('pemusnahan.index')->with('error', 'Tidak ada data yang bisa dimusnahkan.');
        }

        $file = $request->file('bukti');
        $path = $file->store('pemusnahan', 'public');

        try {
            DB::transaction(function () use ($data, $antrean, $path, $file) {
                $pemusnahan = Pemusnahan::create([
                    'nomor' => $data['nomor'],
                    'tanggal' => $data['tanggal'],
                    'pelaksana' => $data['pelaksana'],
                    'bukti_path' => $path,
                    'bukti_nama_asli' => $file->getClientOriginalName(),
                    'dibuat_oleh' => Auth::id(),
                ]);

                foreach ($antrean as $item) {
                    $item->update(['pemusnahan_id' => $pemusnahan->id]);

                    if ($item->tipe === 'dokumen') {
                        Dokumen::whereKey($item->dokumen_id)->update([
                            'status' => 'pemusnahan',
                            'lokasi_arsip_fisik' => '-',
                            'nomor_rak' => null,
                            'no_tingkat' => null,
                            'no_box_file' => null,
                        ]);
                    } else {
                        Sampel::whereKey($item->sampel_id)->update([
                            'status' => 'pemusnahan',
                            'nomor_ruang_arsip' => '-',
                            'nama_ruang_arsip' => '-',
                        ]);
                    }
                }
            });
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($path);

            return redirect()->route('pemusnahan.index')->with('error', 'Pemusnahan gagal diproses: ' . $e->getMessage());
        }

        foreach ($antrean as $item) {
            $this->catatLog($item);
        }

        return redirect()->route('pemusnahan.index')->with('success', "{$antrean->count()} data berhasil dimusnahkan dengan nomor {$data['nomor']}. Lokasi fisik diubah menjadi '-'.");
    }

    private function admin(): bool
    {
        return Auth::user()->role === 'admin';
    }

    private function layout(): string
    {
        return $this->admin() ? 'admin.layout' : 'layouts.user';
    }

    private function lokasiDokumen(Dokumen $dokumen): ?string
    {
        if (filled($dokumen->lokasi_arsip_fisik) && $dokumen->lokasi_arsip_fisik !== '-') {
            return $dokumen->lokasi_arsip_fisik;
        }

        $bagian = [];

        if (filled($dokumen->nomor_rak)) {
            $bagian[] = 'Rak ' . $dokumen->nomor_rak;
        }
        if (filled($dokumen->no_tingkat)) {
            $bagian[] = 'Tingkat ' . $dokumen->no_tingkat;
        }
        if (filled($dokumen->no_box_file)) {
            $bagian[] = 'Box ' . $dokumen->no_box_file;
        }

        return $bagian ? implode(' / ', $bagian) : null;
    }

    private function catatLog(PemusnahanItem $item): void
    {
        try {
            if ($item->tipe === 'dokumen') {
                LogAktivitas::catat('memusnahkan', $item->dokumen_id, $item->judul);
                return;
            }

            DB::table('log_aktivitas')->insert([
                'user_id' => Auth::id(),
                'sampel_id' => $item->sampel_id,
                'aksi' => 'memusnahkan_sampel',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }
    public function destroyRiwayat($id)
    {
        $riwayat = Pemusnahan::with('items')->findOrFail($id);

        abort_unless($this->admin() || $riwayat->dibuat_oleh === Auth::id(), 403);

        DB::transaction(function () use ($riwayat) {
            if ($riwayat->bukti_path && Storage::disk('public')->exists($riwayat->bukti_path)) {
                Storage::disk('public')->delete($riwayat->bukti_path);
            }

            foreach ($riwayat->items as $item) {
                $item->delete();
            }

            $riwayat->delete();
        });

        return redirect()->route('pemusnahan.index')->with('success', 'Riwayat pemusnahan berhasil dihapus.');
    }

    public function destroyRiwayatMassal(Request $request)
    {
        $mode = $request->input('mode_riwayat', 'terpilih');
        
        $query = Pemusnahan::with('items')
            ->when(! $this->admin(), function ($q) {
                $q->where('dibuat_oleh', Auth::id());
            });

        if ($mode === 'terpilih') {
            $data = $request->validate([
                'ids_riwayat' => ['required', 'array', 'min:1'],
                'ids_riwayat.*' => ['integer'],
            ], [
                'ids_riwayat.required' => 'Pilih minimal satu riwayat yang akan dihapus.',
            ]);

            $query->whereIn('id', $data['ids_riwayat']);
        }

        $riwayatList = $query->get();

        if ($riwayatList->isEmpty()) {
            return redirect()->route('pemusnahan.index')->with('error', 'Tidak ada riwayat yang dapat dihapus.');
        }

        DB::transaction(function () use ($riwayatList) {
            foreach ($riwayatList as $riwayat) {
                if ($riwayat->bukti_path && Storage::disk('public')->exists($riwayat->bukti_path)) {
                    Storage::disk('public')->delete($riwayat->bukti_path);
                }

                foreach ($riwayat->items as $item) {
                    $item->delete();
                }
                
                $riwayat->delete();
            }
        });

        $jumlah = $riwayatList->count();
        return redirect()->route('pemusnahan.index')->with('success', "{$jumlah} riwayat pemusnahan berhasil dihapus.");
    }
}