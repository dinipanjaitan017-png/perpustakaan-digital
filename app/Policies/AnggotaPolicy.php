<?php

namespace App\Policies;

use App\Models\Anggota;
use App\Models\User;

class AnggotaPolicy
{
    /**
     * Riwayat peminjaman hanya boleh dilihat pemiliknya sendiri.
     * Admin/Petugas (permission "kelola peminjaman") boleh melihat data operasional.
     * Pengecekan berdasarkan identitas user yang login, bukan hanya permission.
     */
    public function viewRiwayat(User $user, Anggota $anggota): bool
    {
        if ($anggota->user_id === $user->id) {
            return $user->can('melihat riwayat peminjaman sendiri');
        }

        return $user->can('kelola peminjaman');
    }
}
