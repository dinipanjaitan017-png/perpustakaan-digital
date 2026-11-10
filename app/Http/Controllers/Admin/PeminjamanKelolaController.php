<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/** Pengelolaan peminjaman oleh admin & petugas. */
class PeminjamanKelolaController extends Controller
{
    public function index(): View
    {
        $peminjamans = Peminjaman::with(['buku', 'anggota'])->latest('id')->get();

        return view('peminjaman.kelola', compact('peminjamans'));
    }

    public function setujui(Peminjaman $peminjaman): RedirectResponse
    {
        if ($peminjaman->status !== 'diajukan') {
            return back()->with('error', 'Hanya pengajuan yang bisa disetujui.');
        }

        $peminjaman->update([
            'status' => 'dipinjam',
            'tanggal_pinjam' => now()->toDateString(),
        ]);

        return back()->with('status', 'Peminjaman disetujui.');
    }

    public function tolak(Peminjaman $peminjaman): RedirectResponse
    {
        if ($peminjaman->status !== 'diajukan') {
            return back()->with('error', 'Hanya pengajuan yang bisa ditolak.');
        }

        $peminjaman->update(['status' => 'ditolak']);

        return back()->with('status', 'Peminjaman ditolak.');
    }
}
