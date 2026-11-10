<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Mengarahkan user ke dashboard sesuai role/permission.
     */
    public function index(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->can('akses dashboard admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->can('akses dashboard petugas')) {
            return redirect()->route('petugas.dashboard');
        }

        return redirect()->route('buku.index');
    }

    public function admin(): View
    {
        return view('admin.dashboard', [
            'totalBuku' => Buku::count(),
            'totalPengguna' => User::count(),
            'diajukan' => Peminjaman::where('status', 'diajukan')->count(),
            'dipinjam' => Peminjaman::where('status', 'dipinjam')->count(),
        ]);
    }

    public function petugas(): View
    {
        return view('petugas.dashboard', [
            'totalBuku' => Buku::count(),
            'diajukan' => Peminjaman::where('status', 'diajukan')->count(),
            'dipinjam' => Peminjaman::where('status', 'dipinjam')->count(),
        ]);
    }
}
