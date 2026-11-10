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
     * [SOAL 2] Diperbaiki: nilai $judul tidak lagi digabungkan langsung ke
     * string SQL, melainkan dikirim lewat parameter binding ("?"). Driver
     * database yang melakukan escaping terhadap nilainya, sehingga payload
     * seperti ' OR '1'='1 diperlakukan sebagai teks biasa, bukan bagian dari
     * logika query (menutup celah A2 / SQL Injection).
     */
    public function cari(Request $request): JsonResponse
    {
        $judul = $request->query('judul');

        $hasil = DB::select('select * from bukus where judul = ?', [$judul]);

        return response()->json($hasil);
    }

    /**
     * Hapus buku (route DELETE /buku/{buku}).
     *
     * [SOAL 2] Menggantikan route GET /buku/{buku}/hapus pada A3. Karena
     * memakai verb DELETE dan form dengan @csrf, Laravel memverifikasi token
     * CSRF sebelum method ini dijalankan, sehingga tautan di jebakan.html
     * (yang hanya berupa <a href> biasa / GET) tidak lagi bisa memicu
     * penghapusan data (menutup celah A3 / CSRF).
     */
    public function destroy(Buku $buku): \Illuminate\Http\RedirectResponse
    {
        $buku->delete();

        return redirect()->route('buku.index')->with('status', 'Buku berhasil dihapus.');
    }
}
