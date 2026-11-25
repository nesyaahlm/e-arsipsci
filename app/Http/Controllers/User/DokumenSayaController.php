<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DokumenSayaController extends Controller
{
    private const BATAS_LABEL = 100;

    public function index(Request $request): View
    {
        $kata = trim((string) $request->query('q', ''));
        $userId = $request->user()->id;

        $dokumen = $this->kueriCari($userId, $kata)
            ->orderByDesc('dokumen.created_at')
            ->orderByDesc('dokumen.id')
            ->paginate(10)
            ->withQueryString()
            ->through(fn ($baris) => $this->susun($baris));

        $jumlahLabel = $this->denganLokasi($this->kueriCari($userId, $kata))->count();

        return view('user.dokumen-saya.index', [
            'dokumen' => $dokumen,
            'kata' => $kata,
            'jumlahLabel' => $jumlahLabel,
            ...app(UnggahDokumenController::class)->dataForm($request->user()),
        ]);
    }

    public function show(Request $request, int $dokumen): View
    {
        $baris = $this->kueri($request->user()->id)
            ->where('dokumen.id', $dokumen)
            ->first();

        abort_if($baris === null, 404);

        return view('user.dokumen-saya.show', [
            'dokumen' => $this->susun($baris),
            'kembali' => $this->urlKembali(),
        ]);
    }

    public function labelBox(Request $request, int $dokumen): Response
    {
        $baris = $this->kueri($request->user()->id)
            ->where('dokumen.id', $dokumen)
            ->first();

        abort_if($baris === null, 404);

        $data = $this->susun($baris);

        abort_unless($data['lokasi_terisi'], 404);

        $nama = trim((string) preg_replace('/[^A-Za-z0-9]+/', '-', (string) $data['nomor']), '-');
        $nama = 'Label-Box-'.($nama !== '' ? $nama : $data['id']).'.pdf';

        return $this->cetakLabel([$data], $nama);
    }

    public function labelBoxSemua(Request $request): Response|RedirectResponse
    {
        $kata = trim((string) $request->query('q', ''));

        $daftar = $this->denganLokasi($this->kueriCari($request->user()->id, $kata))
            ->orderByDesc('dokumen.created_at')
            ->orderByDesc('dokumen.id')
            ->limit(self::BATAS_LABEL)
            ->get()
            ->map(fn ($baris) => $this->susun($baris))
            ->filter(fn ($data) => $data['lokasi_terisi'])
            ->values()
            ->all();

        if ($daftar === []) {
            return redirect()->route('user.dokumen-saya.index')
                ->with('label_error', 'Belum ada dokumen yang memiliki lokasi box untuk dibuatkan label.');
        }

        $nama = 'Label-Box-Semua-'.now('Asia/Jakarta')->format('Ymd-His').'.pdf';

        return $this->cetakLabel($daftar, $nama);
    }

    public function labelBoxTerpilih(Request $request): Response|RedirectResponse
    {
        $ids = collect($request->input('ids', []))
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return redirect()->route('user.dokumen-saya.index')
                ->with('label_error', 'Pilih minimal satu dokumen untuk diunduh labelnya.');
        }

        if ($ids->count() > self::BATAS_LABEL) {
            return redirect()->route('user.dokumen-saya.index')
                ->with('label_error', 'Maksimal '.self::BATAS_LABEL.' label dalam sekali unduh.');
        }

        $daftar = $this->denganLokasi($this->kueri($request->user()->id))
            ->whereIn('dokumen.id', $ids->all())
            ->orderByDesc('dokumen.created_at')
            ->orderByDesc('dokumen.id')
            ->get()
            ->map(fn ($baris) => $this->susun($baris))
            ->filter(fn ($data) => $data['lokasi_terisi'])
            ->values()
            ->all();

        if ($daftar === []) {
            return redirect()->route('user.dokumen-saya.index')
                ->with('label_error', 'Dokumen yang dipilih belum memiliki lokasi box untuk dibuatkan label.');
        }

        $nama = 'Label-Box-Terpilih-'.now('Asia/Jakarta')->format('Ymd-His').'.pdf';

        return $this->cetakLabel($daftar, $nama);
    }

    private function cetakLabel(array $daftar, string $nama): Response
    {
        @set_time_limit(120);

        $html = view('user.dokumen-saya.label-pdf', ['daftar' => $daftar])->render();

        $opsi = new Options;
        $opsi->set('isRemoteEnabled', false);
        $opsi->set('defaultFont', 'DejaVu Sans');

        $pdf = new Dompdf($opsi);
        $pdf->loadHtml($html, 'UTF-8');
        $pdf->setPaper('A5', 'portrait');
        $pdf->render();

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$nama.'"',
        ]);
    }

    private function kueriCari(int $userId, string $kata): Builder
    {
        return $this->kueri($userId)
            ->when($kata !== '', function (Builder $query) use ($kata) {
                $query->where(function (Builder $cari) use ($kata) {
                    $cari->where('dokumen.judul', 'like', '%'.$kata.'%')
                        ->orWhere('dokumen.nomor', 'like', '%'.$kata.'%');
                });
            });
    }

    private function denganLokasi(Builder $kueri): Builder
    {
        return $kueri->where(function (Builder $lokasi) {
            foreach (['dokumen.nomor_rak', 'dokumen.no_tingkat', 'dokumen.no_box_file'] as $kolom) {
                $lokasi->orWhere(function (Builder $isi) use ($kolom) {
                    $isi->whereNotNull($kolom)->where($kolom, '!=', '');
                });
            }
        });
    }

    private function kueri(int $userId): Builder
    {
        return DB::table('dokumen')
            ->leftJoin('layanan', 'layanan.id', '=', 'dokumen.layanan_id')
            ->leftJoin('bagian', 'bagian.id', '=', 'dokumen.bagian_id')
            ->leftJoin('pemusnahan_item', 'pemusnahan_item.dokumen_id', '=', 'dokumen.id')
            ->whereNull('dokumen.deleted_at')
            ->where('dokumen.diunggah_oleh', $userId)
            ->select([
                'dokumen.id',
                'dokumen.nomor',
                'dokumen.judul',
                'dokumen.deskripsi',
                'dokumen.unit',
                'dokumen.tanggal_dokumen',
                'dokumen.tahun_arsip',
                'dokumen.nomor_rak',
                'dokumen.no_tingkat',
                'dokumen.no_box_file',
                'dokumen.lokasi_arsip_fisik',
                'dokumen.file_nama_asli',
                'dokumen.file_ukuran',
                'dokumen.retensi_sampai',
                'dokumen.status',
                'dokumen.file_path',
                'dokumen.created_at',
                DB::raw('COALESCE(bagian.nama, layanan.nama) as portofolio'),
                'pemusnahan_item.lokasi_sebelumnya as lokasi_semula',
            ]);
    }

    private function susun(object $d): array
    {
        $tanggal = Carbon::parse($d->tanggal_dokumen)->locale('id');
        $diunggah = Carbon::parse($d->created_at)->locale('id');

        return [
            'id' => $d->id,
            'nomor' => $d->nomor,
            'judul' => $d->judul,
            'status' => $d->status,
            'deskripsi' => $d->deskripsi,
            'nomor_rak' => $d->nomor_rak,
            'no_tingkat' => $d->no_tingkat,
            'no_box_file' => $d->no_box_file,
            'lokasi_terisi' => collect([$d->nomor_rak, $d->no_tingkat, $d->no_box_file])
                ->contains(fn ($nilai) => filled($nilai)),
            'lokasi_lama' => $d->lokasi_arsip_fisik,
            'lokasi_semula' => $this->bersihkanLokasi($d->lokasi_semula),
            'unit_kerja' => $d->unit,
            'portofolio' => $d->portofolio,
            'tahun' => filled($d->tahun_arsip) ? $d->tahun_arsip : (string) $tanggal->year,
            'tanggal' => $tanggal->translatedFormat('d F Y'),
            'berkas' => filled($d->file_nama_asli) ? $d->file_nama_asli : basename((string) $d->file_path),
            'tipe' => strtoupper(pathinfo((string) $d->file_path, PATHINFO_EXTENSION)) ?: '-',
            'pratinjau_gambar' => strtolower(pathinfo((string) $d->file_path, PATHINFO_EXTENSION)) !== 'pdf',
            'ukuran' => $this->ukuran((int) $d->file_ukuran),
            'diunggah' => $diunggah->translatedFormat('d F Y'),
            'diunggah_singkat' => $diunggah->translatedFormat('d M Y'),
            'retensi' => $d->retensi_sampai
                ? Carbon::parse($d->retensi_sampai)->locale('id')->translatedFormat('d F Y')
                : null,
            'berkas_ada' => Storage::disk('public')->exists($d->file_path),
            'bisa_pratinjau' => in_array(strtolower(pathinfo($d->file_path, PATHINFO_EXTENSION)), ['pdf', 'png', 'jpg', 'jpeg'], true)
                && Storage::disk('public')->exists($d->file_path),
        ];
    }

    private function bersihkanLokasi(?string $lokasi): ?string
    {
        $lokasi = trim((string) $lokasi, " \t\n\r\0\x0B/-");

        return $lokasi !== '' ? $lokasi : null;
    }

    private function urlKembali(): string
    {
        $arsip = route('user.arsip.index');
        $sebelumnya = url()->previous();

        if (
            parse_url($sebelumnya, PHP_URL_PATH) === parse_url($arsip, PHP_URL_PATH)
            && parse_url($sebelumnya, PHP_URL_HOST) === parse_url($arsip, PHP_URL_HOST)
        ) {
            return $sebelumnya;
        }

        return route('user.dokumen-saya.index');
    }

    private function ukuran(int $byte): string
    {
        if ($byte >= 1048576) {
            return number_format($byte / 1048576, 1, ',', '.').' MB';
        }

        if ($byte >= 1024) {
            return number_format($byte / 1024, 0, ',', '.').' KB';
        }

        return $byte.' B';
    }
}