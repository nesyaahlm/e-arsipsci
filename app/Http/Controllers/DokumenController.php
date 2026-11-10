<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\JenisDokumen;
use App\Models\Layanan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DokumenController extends Controller
{
    public function index(Request $request)
    {
        $query = Dokumen::with(['layanan', 'jenisDokumen', 'pengunggah'])->latest();

        if ($request->filled('cari')) {
            $cari = $request->cari;
            $query->where(function ($q) use ($cari) {
                $q->where('nomor', 'like', "%{$cari}%")
                  ->orWhere('judul', 'like', "%{$cari}%");
            });
        }

        if ($request->filled('layanan_id')) {
            $query->where('layanan_id', $request->layanan_id);
        }

        if ($request->filled('jenis_dokumen_id')) {
            $query->where('jenis_dokumen_id', $request->jenis_dokumen_id);
        }

        $dokumen = $query->paginate(10)->withQueryString();

        $layananList = Layanan::with('jenisDokumen')->orderBy('nama')->get();
        $jenisList = JenisDokumen::orderBy('nama')->get();

        return view('dokumen.index', compact('dokumen', 'layananList', 'jenisList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'layanan_id' => ['required', 'exists:layanan,id'],
            'jenis_dokumen_id' => ['required', 'exists:jenis_dokumen,id'],
            'tanggal_dokumen' => ['required', 'date'],
            'unit' => ['nullable', 'string', 'max:100'],
            'nomor_rak' => ['nullable', 'string', 'max:50'],
            'no_tingkat' => ['nullable', 'string', 'max:50'],
            'no_box' => ['nullable', 'string', 'max:50'],
            'deskripsi' => ['nullable', 'string'],
            'file_dokumen' => ['required', 'file', 'mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg', 'max:20480'],
        ], [
            'judul.required' => 'Judul dokumen wajib diisi.',
            'layanan_id.required' => 'Layanan wajib dipilih.',
            'jenis_dokumen_id.required' => 'Jenis dokumen wajib dipilih.',
            'tanggal_dokumen.required' => 'Tanggal dokumen wajib diisi.',
            'file_dokumen.required' => 'Berkas file dokumen wajib diunggah.',
            'file_dokumen.mimes' => 'Format berkas harus berupa PDF, DOC, DOCX, XLS, XLSX, PNG, atau JPG.',
            'file_dokumen.max' => 'Ukuran berkas file dokumen melebihi batas maksimal 20 MB.',
        ]);

        try {
            $jenisDokumen = JenisDokumen::findOrFail($request->jenis_dokumen_id);
            $tanggalDokumen = Carbon::parse($request->tanggal_dokumen);
            $tahun = $tanggalDokumen->year;

            $prefix = "{$jenisDokumen->kode}/CLC/{$tahun}/";
            $dokumenTerakhir = Dokumen::withTrashed()
                ->where('nomor', 'like', "{$prefix}%")
                ->orderBy('id', 'desc')
                ->first();

            $nomorUrut = 1;
            if ($dokumenTerakhir && $dokumenTerakhir->nomor) {
                $bagianNomor = explode('/', $dokumenTerakhir->nomor);
                $urutanTerakhir = end($bagianNomor);
                $nomorUrut = intval($urutanTerakhir) + 1;
            }

            $nomorOtomatis = sprintf("%s%04d", $prefix, $nomorUrut);

            $retensiSampai = null;
            if ($jenisDokumen->retensi_tahun) {
                $retensiSampai = $tanggalDokumen->copy()->addYears($jenisDokumen->retensi_tahun);
            }

            $file = $request->file('file_dokumen');
            $namaAsli = $file->getClientOriginalName();
            $ukuran = $file->getSize();
            $path = $file->store('dokumen', 'public');

            $lokasiParts = [];
            if ($request->filled('nomor_rak')) {
                $lokasiParts[] = 'Rak ' . trim($request->nomor_rak);
            }
            if ($request->filled('no_tingkat')) {
                $lokasiParts[] = 'Tingkat ' . trim($request->no_tingkat);
            }
            if ($request->filled('no_box')) {
                $lokasiParts[] = 'Box ' . trim($request->no_box);
            }
            $lokasiArsipFisik = !empty($lokasiParts) ? implode(' / ', $lokasiParts) : null;

            Dokumen::create([
                'nomor' => $nomorOtomatis,
                'judul' => $validated['judul'],
                'layanan_id' => $validated['layanan_id'],
                'jenis_dokumen_id' => $validated['jenis_dokumen_id'],
                'unit' => $validated['unit'] ?? null,
                'tanggal_dokumen' => $validated['tanggal_dokumen'],
                'retensi_sampai' => $retensiSampai,
                'deskripsi' => $validated['deskripsi'] ?? null,
                'lokasi_arsip_fisik' => $lokasiArsipFisik,
                'file_path' => $path,
                'file_nama_asli' => $namaAsli,
                'file_ukuran' => $ukuran,
                'diunggah_oleh' => Auth::id(),
            ]);

            return redirect()->route('dokumen.index')->with('success', "Dokumen berhasil diunggah dengan nomor {$nomorOtomatis}.");
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem saat menyimpan dokumen: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Dokumen $dokumen)
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'unit' => ['nullable', 'string', 'max:100'],
            'nomor_rak' => ['nullable', 'string', 'max:50'],
            'no_tingkat' => ['nullable', 'string', 'max:50'],
            'no_box' => ['nullable', 'string', 'max:50'],
            'deskripsi' => ['nullable', 'string'],
            'file_dokumen' => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg', 'max:20480'],
        ], [
            'judul.required' => 'Judul dokumen wajib diisi.',
            'file_dokumen.mimes' => 'Format berkas harus berupa PDF, DOC, DOCX, XLS, XLSX, PNG, atau JPG.',
            'file_dokumen.max' => 'Ukuran berkas file dokumen melebihi batas maksimal 20 MB.',
        ]);

        $lokasiParts = [];
        if ($request->filled('nomor_rak')) {
            $lokasiParts[] = 'Rak ' . trim($request->nomor_rak);
        }
        if ($request->filled('no_tingkat')) {
            $lokasiParts[] = 'Tingkat ' . trim($request->no_tingkat);
        }
        if ($request->filled('no_box')) {
            $lokasiParts[] = 'Box ' . trim($request->no_box);
        }
        $lokasiArsipFisik = !empty($lokasiParts) ? implode(' / ', $lokasiParts) : null;

        $updateData = [
            'judul' => $validated['judul'],
            'unit' => $validated['unit'] ?? null,
            'lokasi_arsip_fisik' => $lokasiArsipFisik,
            'deskripsi' => $validated['deskripsi'] ?? null,
        ];

        if ($request->hasFile('file_dokumen')) {
            $file = $request->file('file_dokumen');
            
            if (Storage::disk('public')->exists($dokumen->file_path)) {
                Storage::disk('public')->delete($dokumen->file_path);
            }

            $updateData['file_path'] = $file->store('dokumen', 'public');
            $updateData['file_nama_asli'] = $file->getClientOriginalName();
            $updateData['file_ukuran'] = $file->getSize();
        }

        $dokumen->update($updateData);

        return redirect()->route('dokumen.index')->with('success', 'Dokumen berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $dokumen = Dokumen::withTrashed()->find($id);

        if ($dokumen && !$dokumen->trashed()) {
            $dokumen->delete();
        }

        return redirect()->route('dokumen.index')->with('success', 'Dokumen berhasil dipindahkan ke arsip terhapus.');
    }

    public function download(Dokumen $dokumen)
    {
        if (!Storage::disk('public')->exists($dokumen->file_path)) {
            return back()->with('error', 'Berkas fisik tidak ditemukan di server.');
        }

        return Storage::disk('public')->download($dokumen->file_path, $dokumen->file_nama_asli);
    }

    public function preview(Dokumen $dokumen)
    {
        if (!Storage::disk('public')->exists($dokumen->file_path)) {
            return abort(404, 'Berkas fisik tidak ditemukan.');
        }

        $filePath = Storage::disk('public')->path($dokumen->file_path);
        $mimeType = Storage::disk('public')->mimeType($dokumen->file_path);

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $dokumen->file_nama_asli . '"'
        ]);
    }
}