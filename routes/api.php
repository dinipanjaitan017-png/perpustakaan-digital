<?php

use App\Http\Controllers\Api\BukuController;
use App\Http\Controllers\Api\RekapController;
use Illuminate\Support\Facades\Route;

// Group "web" dipakai agar API ini memakai session + CSRF yang sama dengan halaman web,
// sehingga middleware "auth" dan permission Spatie bisa dipakai.
Route::middleware(['web', 'auth'])->group(function () {

    // Baca data buku: semua role yang boleh melihat daftar buku
    Route::apiResource('buku', BukuController::class)
        ->only(['index', 'show'])
        ->middleware('permission:lihat daftar buku')
        ->names('api.buku');

    // Tambah / ubah / hapus: hanya yang boleh mengelola data buku
    Route::apiResource('buku', BukuController::class)
        ->only(['store', 'update', 'destroy'])
        ->middleware('permission:kelola data buku')
        ->names('api.buku');

    // Rekap: hanya yang boleh melihat laporan
    Route::middleware('permission:lihat laporan perpustakaan')->group(function () {
        Route::get('rekap/buku-per-kategori', [RekapController::class, 'bukuPerKategori']);
        Route::get('rekap/peminjaman-per-buku', [RekapController::class, 'peminjamanPerBuku']);
    });
});
