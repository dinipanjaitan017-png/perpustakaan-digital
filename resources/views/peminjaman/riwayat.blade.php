<x-app-layout>
    <x-slot name="header">
        <h2 class="judul-halaman">Riwayat Peminjaman: {{ $anggota->nama }}</h2>
    </x-slot>

    <div class="bagian">
        <div class="container">
            <div class="kartu tabel-scroll">
                <table class="tabel">
                    <thead>
                        <tr>
                            <th>Buku</th><th>Tanggal Pinjam</th>
                            <th>Tanggal Kembali</th><th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($peminjamans as $p)
                            <tr>
                                <td>{{ $p->buku->judul ?? '-' }}</td>
                                <td>{{ $p->tanggal_pinjam }}</td>
                                <td>{{ $p->tanggal_kembali ?? '-' }}</td>
                                <td class="kapital">{{ $p->status }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="kosong">Belum ada riwayat peminjaman.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
