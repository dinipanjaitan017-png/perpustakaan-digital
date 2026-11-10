<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * Fitur peminjaman dari sisi pengguna (siswa/petugas/admin sebagai peminjam).
 * Semua data selalu dibatasi pada anggota milik user yang sedang login.
 */
class PeminjamanController extends Controller
{
    public function ajukan(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'buku_id' => ['required', 'integer', 'exists:bukus,id'],
        ]);

        $anggota = $request->user()->anggotaProfil();
        $buku = Buku::findOrFail($data['buku_id']);

        $sudahAda = Peminjaman::where('anggota_id', $anggota->id)
            ->where('buku_id', $buku->id)
            ->whereIn('status', ['diajukan', 'dipinjam'])
            ->exists();

        if ($sudahAda) {
            return back()->with('error', 'Kamu sudah mengajukan/meminjam buku ini.');
        }

        Peminjaman::create([
            'anggota_id' => $anggota->id,
            'buku_id' => $buku->id,
            'tanggal_pinjam' => now()->toDateString(),
            'status' => 'diajukan',
        ]);

        return redirect()->route('peminjaman.saya')
            ->with('status', 'Pengajuan peminjaman berhasil dikirim.');
    }

    /** Status peminjaman milik sendiri (yang masih berjalan). */
    public function saya(Request $request): View
    {
        $anggota = $request->user()->anggotaProfil();

        $peminjamans = $anggota->peminjamans()
            ->with('buku')
            ->whereIn('status', ['diajukan', 'dipinjam'])
            ->latest('id')
            ->get();

        return view('peminjaman.saya', compact('peminjamans', 'anggota'));
    }

    /** Pintasan ke riwayat milik sendiri. */
    public function riwayatSaya(Request $request): RedirectResponse
    {
        return redirect()->route('riwayat.show', $request->user()->anggotaProfil());
    }

    /** Riwayat peminjaman. Dicek berdasarkan identitas pemilik (Policy). */
    public function riwayat(Anggota $anggota): View
    {
        Gate::authorize('viewRiwayat', $anggota);

        $peminjamans = $anggota->peminjamans()->with('buku')->latest('id')->get();

        return view('peminjaman.riwayat', compact('peminjamans', 'anggota'));
    }
}
