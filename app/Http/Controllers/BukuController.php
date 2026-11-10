<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\View\View;

class BukuController extends Controller
{
    /** Daftar buku (semua role yang login, hanya lihat). */
    public function index(): View
    {
        $bukus = Buku::with('kategori')->orderBy('judul')->get();

        return view('buku.index', compact('bukus'));
    }

    /** Detail buku. */
    public function show(Buku $buku): View
    {
        $buku->load('kategori');

        return view('buku.show', compact('buku'));
    }

    /** Halaman pengelolaan data buku (CRUD) - admin & petugas. */
    public function kelola(): View
    {
        $kategoris = Kategori::all();

        return view('buku.kelola', compact('kategoris'));
    }
}
