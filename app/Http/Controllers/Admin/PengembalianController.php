<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PengembalianController extends Controller
{
    public function index(): View
    {
        $peminjamans = Peminjaman::with(['buku', 'anggota'])
            ->whereIn('status', ['dipinjam', 'dikembalikan'])
            ->latest('id')
            ->get();

        return view('pengembalian.kelola', compact('peminjamans'));
    }

    public function catat(Peminjaman $peminjaman): RedirectResponse
    {
        if ($peminjaman->status !== 'dipinjam') {
            return back()->with('error', 'Buku ini tidak sedang dipinjam.');
        }

        $peminjaman->update([
            'status' => 'dikembalikan',
            'tanggal_kembali' => now()->toDateString(),
        ]);

        return back()->with('status', 'Pengembalian buku dicatat.');
    }
}
