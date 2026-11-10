<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    /**
     * Pencarian buku berdasarkan judul (route GET /buku-cari).
     *
     * [SOAL 1 - A2] RENTAN SQL INJECTION: nilai $judul dari query string
     * digabungkan langsung ke dalam string SQL, sehingga payload seperti
     * ' OR '1'='1 mengubah logika query dan membocorkan seluruh data.
     */
    public function cari(Request $request): JsonResponse
    {
        $judul = $request->query('judul');

        $hasil = DB::select("select * from bukus where judul = '{$judul}'");

        return response()->json($hasil);
    }
}
