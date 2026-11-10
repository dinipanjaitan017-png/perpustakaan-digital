<?php

namespace App\Providers;

use App\Models\Peminjaman;
use App\Policies\PeminjamanPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /**
         * [SOAL 3] Gate terpusat untuk pengelolaan buku.
         *
         * Hanya role admin dan petugas yang diizinkan. Dipakai lewat
         * middleware "can:kelola-buku" pada route tambah/ubah/hapus buku
         * (routes/web.php & routes/api.php) dan lewat @can('kelola-buku')
         * pada tampilan, menggantikan pemeriksaan role/permission yang
         * sebelumnya dipasang terpisah-pisah pada tiap route.
         */
        Gate::define('kelola-buku', function ($user) {
            return $user->hasAnyRole(['admin', 'petugas']);
        });

        // [SOAL 4] Daftarkan PeminjamanPolicy secara eksplisit (tidak hanya
        // mengandalkan konvensi auto-discovery nama kelas).
        Gate::policy(Peminjaman::class, PeminjamanPolicy::class);
    }
}
