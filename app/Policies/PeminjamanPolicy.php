<?php

namespace App\Policies;

use App\Models\Peminjaman;
use App\Models\User;

class PeminjamanPolicy
{
    /**
     * [SOAL 4] Menutup celah A4 (IDOR).
     *
     * Admin dan petugas boleh melihat peminjaman siapa pun (sesuai peran
     * operasional mereka). Siswa hanya boleh melihat peminjaman miliknya
     * sendiri — dicek lewat relasi anggota->user_id, BUKAN hanya lewat
     * role/permission, karena pemeriksaan role saja tidak bisa membedakan
     * "siswa A" dari "siswa B": keduanya sama-sama punya role siswa,
     * sehingga tanpa pengecekan kepemilikan seperti ini, siswa mana pun
     * tetap bisa membuka data milik siswa lain.
     */
    public function view(User $user, Peminjaman $peminjaman): bool
    {
        if ($user->hasAnyRole(['admin', 'petugas'])) {
            return true;
        }

        return $peminjaman->anggota && $peminjaman->anggota->user_id === $user->id;
    }
}
