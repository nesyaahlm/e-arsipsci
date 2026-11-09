<?php

namespace App\View\Components\User;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;

class Navbar extends Component
{
    public string $nama;

    public string $peran;

    public ?string $fotoUrl;

    public function __construct()
    {
        $user = auth()->user();

        $layanan = $user && $user->layanan_id
            ? DB::table('layanan')->where('id', $user->layanan_id)->value('nama')
            : null;

        $this->nama = $user?->name ?? 'Tamu';
        $this->peran = $user && $user->role === 'user' && $layanan
            ? 'User - '.$layanan
            : ucfirst($user?->role ?? 'user');
        $this->fotoUrl = $user && $user->foto ? asset('storage/'.$user->foto) : null;
    }

    public function render(): View
    {
        return view('components.user.navbar');
    }
}
