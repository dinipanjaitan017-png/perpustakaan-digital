# Modul 5 – Autentikasi & RBAC (Sistem Perpustakaan Digital)

## Langkah menjalankan
```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
php artisan db:seed --class=RolePermissionSeeder   # atau: php artisan migrate:fresh --seed
npm install && npm run build                        # agar class Tailwind baru ikut ter-compile
php artisan serve
php artisan test                                    # menjalankan pengujian otomatis
```

Akun awal (dibuat seeder, password: `password`):

| Role    | Email                       |
|---------|-----------------------------|
| admin   | admin@perpustakaan.test     |
| petugas | petugas@perpustakaan.test   |
| siswa   | siswa@perpustakaan.test     |

## Pemetaan soal -> implementasi
| Soal | Implementasi |
|------|--------------|
| 1 Breeze | `routes/auth.php`, `Auth/*Controller`, view `auth/*`. Password di-hash (`Hash::make` + cast `hashed`). Registrasi otomatis memberi role `siswa` dan membuat data `anggotas` terkait (`RegisteredUserController`). |
| 2 Session & middleware | `guest` membungkus login/register (`routes/auth.php`); `auth` membungkus semua route di `routes/web.php` dan `routes/api.php`. Logout: invalidate session + regenerate token. |
| 3 RBAC 3 role | Role hanya bisa ditetapkan oleh seeder atau admin (`Admin/UserController`). `role` tidak ada di `#[Fillable]` User dan diabaikan oleh form registrasi. |
| 4 Spatie | `RolePermissionSeeder` (3 role, 16 permission, sesuai tabel). Trait `HasRoles` di `User`. Kepemilikan data: `AnggotaPolicy::viewRiwayat` + `ProfileController` memakai `$request->user()`. |
| 5 Proteksi route + Blade | Middleware `auth` + `permission:...` di route (alias di `bootstrap/app.php`). Menu memakai `@can` / `@role` (`layouts/navigation.blade.php`, dashboard, tabel aksi). |

## Perubahan penting pada kode lama
- `routes/api.php`: CRUD buku & rekap sebelumnya terbuka tanpa login; sekarang `web` + `auth` + permission.
- `buku/index.blade.php` (halaman CRUD lama) dipindah ke `buku/kelola.blade.php` (route `/kelola/buku`); `/buku` kini daftar buku read-only.
- `RekapController`: `withCount('peminjaman')` diperbaiki menjadi `peminjamans`; `Kategori` diberi relasi `bukus`.
- Migrasi baru: `anggotas.user_id` dan `peminjamans.status` (diajukan/dipinjam/dikembalikan/ditolak).
- `tests/Feature/ProfileTest.php` disesuaikan (user diberi role siswa).

## Tabel pengujian (Soal 5)
Kolom "Hasil Aktual" diisi setelah menjalankan pengujian manual / `php artisan test --filter=RbacTest`.

| No | Role | Skenario | Diharapkan | Hasil Aktual |
|----|------|----------|------------|--------------|
| 1 | Belum login | Buka `/admin/dashboard` | Redirect ke login | |
| 2 | Admin | Buka dashboard admin | Dapat diakses | |
| 3 | Admin | Tambah/ubah/hapus buku (`/kelola/buku`) | Berhasil | |
| 4 | Admin | Kelola akun & role (`/admin/users`) | Berhasil | |
| 5 | Admin | Akses peminjaman, pengembalian, laporan | Dapat diakses | |
| 6 | Petugas | Buka dashboard petugas | Dapat diakses | |
| 7 | Petugas | Kelola data buku | Berhasil | |
| 8 | Petugas | Proses peminjaman & pengembalian | Berhasil | |
| 9 | Petugas | Buka `/admin/users` atau ubah role | 403 ditolak | |
| 10 | Siswa | Lihat daftar & detail buku | Dapat dilihat | |
| 11 | Siswa | Lihat & ubah profil sendiri | Berhasil | |
| 12 | Siswa | Lihat riwayat sendiri | Dapat dilihat | |
| 13 | Siswa | Buka dashboard admin / `/kelola/buku` | 403 ditolak | |
| 14 | Siswa | Buka `/anggota/{id lain}/riwayat` | 403 ditolak | |
| 15 | Semua | Logout lalu buka halaman terlindungi | Redirect ke login | |
