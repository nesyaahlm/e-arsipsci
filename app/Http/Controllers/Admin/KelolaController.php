<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\PasswordSementaraMail;
use App\Models\Layanan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class KelolaController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $users = User::with('layanan')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        $layanan = Layanan::orderBy('id')->get();

        return view('admin.kelola-user', compact('users', 'layanan', 'q'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'role' => ['required', Rule::in(['admin', 'user'])],
            'layanan_id' => 'required_if:role,user|nullable|exists:layanan,id',
        ], $this->pesan());

        $passwordSementara = Str::password(10, true, true, false, false);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'layanan_id' => $data['layanan_id'] ?? null,
            'status' => 'aktif',
            'password' => $passwordSementara,
        ]);

        $this->catatLog('Menambahkan user ' . $user->name);

        try {
            Mail::to($user->email)->send(new PasswordSementaraMail($user, $passwordSementara));
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('users.index')->with(
                'peringatan',
                'User berhasil dibuat, tetapi email password sementara gagal dikirim. Minta user memakai fitur Lupa Password.'
            );
        }

        return redirect()->route('users.index')->with(
            'sukses',
            'User berhasil ditambahkan. Password sementara sudah dikirim ke ' . $user->email . '.'
        );
    }

    public function update(Request $request, User $user)
    {
        $self = $user->id === Auth::id();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => $self ? 'nullable' : ['required', Rule::in(['admin', 'user'])],
            'layanan_id' => 'required_if:role,user|nullable|exists:layanan,id',
            'status' => $self ? 'nullable' : ['required', Rule::in(['aktif', 'nonaktif'])],
        ], $this->pesan());

        if ($self) {
            $data['role'] = $user->role;
            $data['status'] = $user->status;
        }

        $statusLama = $user->status;

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role = $data['role'];
        $user->layanan_id = $data['layanan_id'] ?? null;
        $user->status = $data['status'];
        $user->save();

        if ($statusLama !== $user->status) {
            $aksi = $user->status === 'nonaktif' ? 'Menonaktifkan ' : 'Mengaktifkan ';
            $this->catatLog($aksi . $user->name);
        } else {
            $this->catatLog('Mengubah data ' . $user->name);
        }

        return redirect()->route('users.index')->with('sukses', 'Data user berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')->with('gagal', 'Kamu tidak bisa menghapus akunmu sendiri.');
        }

        if (DB::table('dokumen')->where('diunggah_oleh', $user->id)->exists()) {
            return redirect()->route('users.index')->with(
                'gagal',
                $user->name . ' sudah pernah mengunggah dokumen sehingga tidak bisa dihapus. Nonaktifkan saja akunnya.'
            );
        }

        if ($user->foto && Storage::disk('public')->exists($user->foto)) {
            Storage::disk('public')->delete($user->foto);
        }

        $nama = $user->name;
        $user->delete();

        $this->catatLog('Menghapus user ' . $nama);

        return redirect()->route('users.index')->with('sukses', 'User berhasil dihapus.');
    }

    private function catatLog(string $keterangan): void
    {
        DB::table('log_aktivitas')->insert([
            'user_id' => Auth::id(),
            'aktivitas' => 'mengelola_user',
            'dokumen_id' => null,
            'keterangan' => Str::limit($keterangan, 255, ''),
        ]);
    }

    private function pesan(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah dipakai user lain.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role tidak valid.',
            'layanan_id.required_if' => 'Layanan wajib dipilih untuk role user.',
            'layanan_id.exists' => 'Layanan tidak ditemukan.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status tidak valid.',
        ];
    }
}