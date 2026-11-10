<x-app-layout>
    <x-slot name="header">
        <h2 class="judul-halaman">Dashboard Petugas</h2>
    </x-slot>

    <div class="bagian">
        <div class="container tumpuk">
            <div class="grid-stat">
                <div class="kartu"><div class="stat-label">Total Buku</div><div class="stat-nilai">{{ $totalBuku }}</div></div>
                @role('admin')
                    <div class="kartu"><div class="stat-label">Total Pengguna</div><div class="stat-nilai">{{ $totalPengguna }}</div></div>
                @endrole
                <div class="kartu"><div class="stat-label">Menunggu Persetujuan</div><div class="stat-nilai">{{ $diajukan }}</div></div>
                <div class="kartu"><div class="stat-label">Sedang Dipinjam</div><div class="stat-nilai">{{ $dipinjam }}</div></div>
            </div>
        </div>
    </div>
</x-app-layout>
