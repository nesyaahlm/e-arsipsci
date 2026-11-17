<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Cabang;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminCabangController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->query('tab', 'admin');
        $cari = $request->query('cari');
        $cabangId = $request->query('cabang_id');
        $status = $request->query('status');

        $totalAdmin = User::where('role', 'admin')->count();
        $totalCabang = Cabang::count();

        $queryAdmin = User::with('cabang')
            ->where('role', 'admin')
            ->when($cari, function ($q, $cari) {
                $q->where(function ($sub) use ($cari) {
                    $sub->where('name', 'like', "%{$cari}%")
                        ->orWhere('email', 'like', "%{$cari}%");
                });
            })
            ->when($cabangId, function ($q, $cabangId) {
                $q->where('cabang_id', $cabangId);
            })
            ->when($status, function ($q, $status) {
                $q->where('status', $status);
            })
            ->orderBy('name', 'asc');

        $daftarAdmin = $queryAdmin->paginate(10, ['*'], 'admin_page')->withQueryString();

        $queryCabang = Cabang::withCount([
            'users as total_admin' => fn ($q) => $q->where('role', 'admin'),
            'dokumen',
            'sampel',
        ])
            ->when($cari, function ($q, $cari) {
                $q->where(function ($sub) use ($cari) {
                    $sub->where('nama', 'like', "%{$cari}%")
                        ->orWhere('kode', 'like', "%{$cari}%")
                        ->orWhere('kota', 'like', "%{$cari}%");
                });
            })
            ->orderBy('nama', 'asc');

        $daftarCabangLengkap = $queryCabang->paginate(10, ['*'], 'cabang_page')->withQueryString();
        $daftarCabang = Cabang::orderBy('nama', 'asc')->get();

        return view('superadmin.admin-cabang.index', compact(
            'daftarAdmin',
            'daftarCabang',
            'daftarCabangLengkap',
            'cari',
            'cabangId',
            'status',
            'tab',
            'totalAdmin',
            'totalCabang'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'cabang_id' => ['required', 'exists:cabang,id'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'admin',
            'cabang_id' => $validated['cabang_id'],
            'status' => 'aktif',
        ]);

        return redirect()
            ->route('superadmin.admin-cabang.index', ['tab' => 'admin'])
            ->with('sukses', 'Akun Admin Cabang berhasil ditambahkan.');
    }

    public function update(Request $request, User $adminCabang): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($adminCabang->id)],
            'cabang_id' => ['required', 'exists:cabang,id'],
            'status' => ['required', 'in:aktif,nonaktif'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $adminCabang->name = $validated['name'];
        $adminCabang->email = $validated['email'];
        $adminCabang->cabang_id = $validated['cabang_id'];
        $adminCabang->status = $validated['status'];

        if (! empty($validated['password'])) {
            $adminCabang->password = Hash::make($validated['password']);
        }

        $adminCabang->save();

        return redirect()
            ->route('superadmin.admin-cabang.index', ['tab' => 'admin'])
            ->with('sukses', 'Data Admin Cabang berhasil diperbarui.');
    }

    public function destroy(User $adminCabang): RedirectResponse
    {
        $memilikiData = DB::table('dokumen')->where('diunggah_oleh', $adminCabang->id)->exists()
            || DB::table('sampel')->where('diunggah_oleh', $adminCabang->id)->exists();

        if ($memilikiData) {
            $adminCabang->status = 'nonaktif';
            $adminCabang->save();

            return redirect()
                ->route('superadmin.admin-cabang.index', ['tab' => 'admin'])
                ->with('sukses', 'Akun Admin dinonaktifkan karena memiliki riwayat berkas arsip yang terhubung.');
        }

        $adminCabang->delete();

        return redirect()
            ->route('superadmin.admin-cabang.index', ['tab' => 'admin'])
            ->with('sukses', 'Akun Admin Cabang berhasil dihapus.');
    }

    public function storeCabang(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:10', 'unique:cabang,kode'],
            'nama' => ['required', 'string', 'max:255'],
            'kota' => ['nullable', 'string', 'max:100'],
            'alamat' => ['nullable', 'string'],
        ]);

        $validated['kode'] = strtoupper($validated['kode']);

        Cabang::create($validated);

        return redirect()
            ->route('superadmin.admin-cabang.index', ['tab' => 'cabang'])
            ->with('sukses', 'Kantor Cabang baru berhasil ditambahkan.');
    }

    public function updateCabang(Request $request, Cabang $cabang): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:10', 'unique:cabang,kode,' . $cabang->id],
            'nama' => ['required', 'string', 'max:255'],
            'kota' => ['nullable', 'string', 'max:100'],
            'alamat' => ['nullable', 'string'],
        ]);

        $validated['kode'] = strtoupper($validated['kode']);

        $cabang->update($validated);

        return redirect()
            ->route('superadmin.admin-cabang.index', ['tab' => 'cabang'])
            ->with('sukses', 'Data Kantor Cabang berhasil diperbarui.');
    }

    public function destroyCabang(Cabang $cabang): RedirectResponse
    {
        $terhubung = $cabang->users()->exists()
            || $cabang->dokumen()->exists()
            || $cabang->sampel()->exists();

        if ($terhubung) {
            return redirect()
                ->route('superadmin.admin-cabang.index', ['tab' => 'cabang'])
                ->with('galat', 'Kantor cabang tidak dapat dihapus karena masih memiliki data Administrator, Dokumen, atau Sampel yang terhubung.');
        }

        $cabang->delete();

        return redirect()
            ->route('superadmin.admin-cabang.index', ['tab' => 'cabang'])
            ->with('sukses', 'Kantor Cabang berhasil dihapus.');
    }
}