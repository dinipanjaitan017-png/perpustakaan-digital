<?php

use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\PeminjamanKelolaController;
use App\Http\Controllers\Admin\PengembalianController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

// Semua route di bawah ini wajib login (middleware auth).
// Pengguna yang belum login otomatis dialihkan ke halaman login.
Route::middleware('auth')->group(function () {

    // Mengarahkan ke dashboard sesuai role
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Dashboard
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])
        ->middleware('permission:akses dashboard admin')->name('admin.dashboard');
    Route::get('/petugas/dashboard', [DashboardController::class, 'petugas'])
        ->middleware('permission:akses dashboard petugas')->name('petugas.dashboard');

    // Profil (hanya milik sendiri: controller memakai $request->user())
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->middleware('permission:lihat profil sendiri')->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->middleware('permission:ubah profil sendiri')->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->middleware('permission:ubah profil sendiri')->name('profile.destroy');

    // Daftar & detail buku (admin, petugas, siswa)
    Route::middleware('permission:lihat daftar buku')->group(function () {
        Route::get('/buku', [BukuController::class, 'index'])->name('buku.index');
        Route::get('/buku/{buku}', [BukuController::class, 'show'])
            ->whereNumber('buku')->name('buku.show');
    });

    // Pengelolaan data buku (admin & petugas)
    Route::get('/kelola/buku', [BukuController::class, 'kelola'])
        ->middleware('permission:kelola data buku')->name('buku.kelola');

    // Peminjaman milik sendiri
    Route::post('/peminjaman', [PeminjamanController::class, 'ajukan'])
        ->middleware('permission:mengajukan peminjaman buku')->name('peminjaman.ajukan');
    Route::get('/peminjaman/saya', [PeminjamanController::class, 'saya'])
        ->middleware('permission:melihat status peminjaman sendiri')->name('peminjaman.saya');
    Route::get('/riwayat', [PeminjamanController::class, 'riwayatSaya'])
        ->middleware('permission:melihat riwayat peminjaman sendiri')->name('riwayat.saya');
    // Dicek lagi di AnggotaPolicy: hanya pemilik (atau staf dengan "kelola peminjaman")
    Route::get('/anggota/{anggota}/riwayat', [PeminjamanController::class, 'riwayat'])
        ->name('riwayat.show');

    // Operasional perpustakaan (admin & petugas)
    Route::get('/kelola/peminjaman', [PeminjamanKelolaController::class, 'index'])
        ->middleware('permission:kelola peminjaman')->name('peminjaman.kelola');
    Route::patch('/kelola/peminjaman/{peminjaman}/setujui', [PeminjamanKelolaController::class, 'setujui'])
        ->middleware('permission:memproses persetujuan peminjaman')->name('peminjaman.setujui');
    Route::patch('/kelola/peminjaman/{peminjaman}/tolak', [PeminjamanKelolaController::class, 'tolak'])
        ->middleware('permission:memproses persetujuan peminjaman')->name('peminjaman.tolak');

    Route::get('/kelola/pengembalian', [PengembalianController::class, 'index'])
        ->middleware('permission:kelola pengembalian')->name('pengembalian.kelola');
    Route::patch('/kelola/pengembalian/{peminjaman}', [PengembalianController::class, 'catat'])
        ->middleware('permission:mencatat pengembalian buku')->name('pengembalian.catat');

    Route::get('/laporan', [LaporanController::class, 'index'])
        ->middleware('permission:lihat laporan perpustakaan')->name('laporan.index');

    // Manajemen akun & role (khusus admin)
    Route::prefix('admin/users')->name('admin.users.')->group(function () {
        Route::middleware('permission:kelola data pengguna')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::patch('/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        });

        Route::patch('/{user}/role', [UserController::class, 'updateRole'])
            ->middleware('permission:kelola role pengguna')->name('role');
    });
});

// Login, register, logout (middleware guest / auth sudah dipasang di dalam file ini)
require __DIR__.'/auth.php';
