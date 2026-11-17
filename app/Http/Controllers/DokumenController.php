<?php

namespace App\Http\Controllers;

use App\Models\Bagian;
use App\Models\Dokumen;
use App\Models\Cabang;
use App\Models\KodeSurat;
use App\Models\LogAktivitas;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DokumenController extends Controller
{
    public function index(Request $request)
    {
        $query = Dokumen::with(['bagian', 'pengunggah'])->latest();

        if ($request->filled('cari')) {
            $cari = $request->cari;
            $query->where(function ($q) use ($cari) {
                $q->where('nomor', 'like', "%{$cari}%")
                    ->orWhere('judul', 'like', "%{$cari}%");
            });
        }

        if ($request->filled('bagian_id')) {
            $query->where('bagian_id', $request->bagian_id);
        }

        $dokumen = $query->paginate(10)->withQueryString();

        $bagianList = Bagian::orderBy('nama')->get();
        $kodeSuratList = KodeSurat::orderBy('kode')->get();

        return view('dokumen.index', compact('dokumen', 'bagianList', 'kodeSuratList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'bagian_id' => ['required', 'exists:bagian,id'],
            'kode_surat_id' => ['nullable', 'exists:kode_surat,id'],
            'tanggal_dokumen' => ['required', 'date'],
            'unit' => ['nullable', 'string', 'max:100'],
            'nomor_rak' => ['nullable', 'string', 'max:50'],
            'no_tingkat' => ['nullable', 'string', 'max:50'],
            'no_box' => ['nullable', 'string', 'max:50'],
            'deskripsi' => ['nullable', 'string'],
            'file_dokumen' => ['required', 'file', 'mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg', 'max:20480'],
        ], [
            'judul.required' => 'Judul dokumen wajib diisi.',
            'bagian_id.required' => 'Bagian wajib dipilih.',
            'bagian_id.exists' => 'Bagian yang dipilih tidak ditemukan.',
            'tanggal_dokumen.required' => 'Tanggal dokumen wajib diisi.',
            'file_dokumen.required' => 'Berkas file dokumen wajib diunggah.',
            'file_dokumen.mimes' => 'Format berkas harus berupa PDF, DOC, DOCX, XLS, XLSX, PNG, atau JPG.',
            'file_dokumen.max' => 'Ukuran berkas file dokumen melebihi batas maksimal 20 MB.',
        ]);

        try {
            $bagian = Bagian::findOrFail($validated['bagian_id']);
            $kodeSurat = !empty($validated['kode_surat_id']) ? KodeSurat::find($validated['kode_surat_id']) : null;

            $user = Auth::user();
            $cabang = $user->cabang ?? Cabang::where('kode', 'CLC')->first();
            $kodeCabang = $cabang ? $cabang->kode : 'CLC';
            $cabangId = $cabang ? $cabang->id : 1;

            $kodeKlasifikasi = $kodeSurat ? $kodeSurat->kode : $bagian->kode;
            $tanggalDokumen = Carbon::parse($validated['tanggal_dokumen']);
            $tahun = $tanggalDokumen->year;

            $bulanRomawi = match ($tanggalDokumen->month) {
                1 => 'I',
                2 => 'II',
                3 => 'III',
                4 => 'IV',
                5 => 'V',
                6 => 'VI',
                7 => 'VII',
                8 => 'VIII',
                9 => 'IX',
                10 => 'X',
                11 => 'XI',
                12 => 'XII',
                default => 'I',
            };

            $polaCari = "%/{$kodeKlasifikasi}/{$kodeCabang}/%/{$tahun}";
            $dokumenTerakhir = Dokumen::withTrashed()
                ->where('nomor', 'like', $polaCari)
                ->orderBy('id', 'desc')
                ->first();

            $nomorUrut = 1;
            if ($dokumenTerakhir && $dokumenTerakhir->nomor) {
                $bagianNomor = explode('/', $dokumenTerakhir->nomor);
                $nomorUrut = intval($bagianNomor[0]) + 1;
            }

            $nomorOtomatis = sprintf('%04d/%s/%s/%s/%d', $nomorUrut, $kodeKlasifikasi, $kodeCabang, $bulanRomawi, $tahun);

            $retensiTahun = $kodeSurat ? $kodeSurat->retensi_aktif : $bagian->retensi_tahun;
            $retensiSampai = null;
            if ($retensiTahun) {
                $retensiSampai = $tanggalDokumen->copy()->addYears($retensiTahun);
            }

            $file = $request->file('file_dokumen');
            $namaAsli = $file->getClientOriginalName();
            $ukuran = $file->getSize();
            $path = $file->store('dokumen', 'public');

            $lokasiArsipFisik = $this->susunLokasi($request);

            $dokumen = Dokumen::create([
                'cabang_id' => $cabangId,
                'kode_surat_id' => $kodeSurat?->id,
                'nomor' => $nomorOtomatis,
                'judul' => $validated['judul'],
                'bagian_id' => $bagian->id,
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

            LogAktivitas::catat('mengunggah', $dokumen->id, $dokumen->judul);

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

        $updateData = [
            'judul' => $validated['judul'],
            'unit' => $validated['unit'] ?? null,
            'lokasi_arsip_fisik' => $this->susunLokasi($request),
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

        LogAktivitas::catat('mengubah', $dokumen->id, $dokumen->judul);

        return redirect()->route('dokumen.index')->with('success', 'Dokumen berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $dokumen = Dokumen::withTrashed()->find($id);

        if ($dokumen && !$dokumen->trashed()) {
            $dokumen->delete();

            LogAktivitas::catat('menghapus', $dokumen->id, $dokumen->judul);
        }

        return redirect()->route('dokumen.index')->with('success', 'Dokumen berhasil dipindahkan ke arsip terhapus.');
    }

    public function download(Dokumen $dokumen)
    {
        if (!Storage::disk('public')->exists($dokumen->file_path)) {
            return back()->with('error', 'Berkas fisik tidak ditemukan di server.');
        }

        LogAktivitas::catat('mengunduh', $dokumen->id, $dokumen->judul);

        return response()->download(storage_path('app/public/' . $dokumen->file_path), $dokumen->file_nama_asli);
    }

    public function preview(Dokumen $dokumen)
    {
        if (!Storage::disk('public')->exists($dokumen->file_path)) {
            return abort(404, 'Berkas fisik tidak ditemukan.');
        }

        LogAktivitas::catat('melihat', $dokumen->id, $dokumen->judul);

        $filePath = Storage::disk('public')->path($dokumen->file_path);
        $mimeType = mime_content_type($filePath);

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $dokumen->file_nama_asli . '"',
        ]);
    }

    private function susunLokasi(Request $request): ?string
    {
        $bagian = [];

        if ($request->filled('nomor_rak')) {
            $bagian[] = 'Rak ' . trim($request->nomor_rak);
        }
        if ($request->filled('no_tingkat')) {
            $bagian[] = 'Tingkat ' . trim($request->no_tingkat);
        }
        if ($request->filled('no_box')) {
            $bagian[] = 'Box ' . trim($request->no_box);
        }

        return $bagian ? implode(' / ', $bagian) : null;
    }
}