<?php

namespace Database\Seeders;

use App\Models\Anggota;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'akses dashboard admin',
            'akses dashboard petugas',
            'lihat daftar buku',
            'kelola data buku',
            'kelola data pengguna',
            'kelola role pengguna',
            'kelola peminjaman',
            'kelola pengembalian',
            'mengajukan peminjaman buku',
            'memproses persetujuan peminjaman',
            'mencatat pengembalian buku',
            'melihat status peminjaman sendiri',
            'melihat riwayat peminjaman sendiri',
            'lihat laporan perpustakaan',
            'lihat profil sendiri',
            'ubah profil sendiri',
        ];

        foreach ($permissions as $nama) {
            Permission::findOrCreate($nama, 'web');
        }

        $umum = [
            'lihat daftar buku',
            'mengajukan peminjaman buku',
            'melihat status peminjaman sendiri',
            'melihat riwayat peminjaman sendiri',
            'lihat profil sendiri',
            'ubah profil sendiri',
        ];

        $operasional = [
            'kelola data buku',
            'kelola peminjaman',
            'kelola pengembalian',
            'memproses persetujuan peminjaman',
            'mencatat pengembalian buku',
            'lihat laporan perpustakaan',
        ];

        $admin = Role::findOrCreate('admin', 'web');
        $petugas = Role::findOrCreate('petugas', 'web');
        $siswa = Role::findOrCreate('siswa', 'web');

        $admin->syncPermissions(array_merge(
            ['akses dashboard admin', 'kelola data pengguna', 'kelola role pengguna'],
            $operasional,
            $umum
        ));

        $petugas->syncPermissions(array_merge(['akses dashboard petugas'], $operasional, $umum));

        $siswa->syncPermissions($umum);

        // Akun awal (role ditetapkan di sini, bukan lewat form registrasi)
        //
        // [SOAL 5c] Password default "password" SENGAJA diganti agar tidak
        // lagi bisa dipakai untuk login (NIM dipakai sebagai contoh payload
        // data uji pada soal-soal audit sebelumnya, bukan di sini).
        // Password diambil dari .env (ADMIN_SEED_PASSWORD) agar tidak
        // di-hardcode ke dalam kode yang ikut di-commit; .env sendiri sudah
        // ada di .gitignore. Jika variabel tidak diisi, dipakai nilai
        // cadangan yang tetap BUKAN "password".
        $passwordAdmin = env('ADMIN_SEED_PASSWORD', 'P3rpus-Admin!Aman');

        $akun = [
            ['Admin Perpustakaan', 'admin@perpustakaan.test', 'admin', $passwordAdmin],
            ['Petugas Perpustakaan', 'petugas@perpustakaan.test', 'petugas', $passwordAdmin],
            ['Siswa Contoh', 'siswa@perpustakaan.test', 'siswa', $passwordAdmin],
        ];

        foreach ($akun as [$nama, $email, $role, $password]) {
            // updateOrCreate dipakai (bukan firstOrCreate) agar password
            // akun yang SUDAH ADA di database ikut ter-update saat seeder
            // dijalankan ulang, sehingga password lama "password" benar-benar
            // tidak berlaku lagi setelah `php artisan db:seed`.
            $user = User::updateOrCreate(
                ['email' => $email],
                ['name' => $nama, 'password' => $password] // di-hash otomatis oleh cast "hashed"
            );
            $user->syncRoles($role);

            Anggota::firstOrCreate(
                ['user_id' => $user->id],
                ['nama' => $nama, 'email' => $email, 'no_telepon' => '-']
            );
        }
    }
}
