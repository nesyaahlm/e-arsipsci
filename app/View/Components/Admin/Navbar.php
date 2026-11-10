<?php

namespace App\View\Components\Admin;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class Navbar extends Component
{
    public string $nama;

    public string $peran;

    public ?string $fotoUrl;

    public function __construct()
    {
        $user = Auth::user();

        $this->nama = $user?->name ?? 'Tamu';
        $this->peran = ucfirst($user?->role ?? 'admin');
        $this->fotoUrl = $user && $user->foto ? asset('storage/'.$user->foto) : null;
    }

    public function render(): View
    {
        return view('components.admin.navbar');
    }
}