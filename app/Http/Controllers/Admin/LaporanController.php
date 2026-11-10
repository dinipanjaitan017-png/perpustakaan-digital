<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function index(): View
    {
        return view('laporan.index', [
            'total' => Peminjaman::count(),
            'diajukan' => Peminjaman::where('status', 'diajukan')->count(),
            'dipinjam' => Peminjaman::where('status', 'dipinjam')->count(),
            'dikembalikan' => Peminjaman::where('status', 'dikembalikan')->count(),
            'ditolak' => Peminjaman::where('status', 'ditolak')->count(),
            'perBuku' => Buku::with('kategori')->withCount('peminjamans')->orderByDesc('peminjamans_count')->get(),
            'perKategori' => Kategori::withCount('bukus')->get(),
        ]);
    }
}
