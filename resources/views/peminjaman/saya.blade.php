<x-app-layout>
    <x-slot name="header">
        <h2 class="judul-halaman">Status Peminjaman Saya</h2>
    </x-slot>

    <div class="bagian">
        <div class="container">
            <div class="kartu tabel-scroll">
                <table class="tabel">
                    <thead>
                        <tr><th>Buku</th><th>Tanggal</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($peminjamans as $p)
                            <tr>
                                <td>{{ $p->buku->judul ?? '-' }}</td>
                                <td>{{ $p->tanggal_pinjam }}</td>
                                <td class="kapital">{{ $p->status }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="kosong">Tidak ada peminjaman yang sedang berjalan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <p class="mt"><a class="tautan" href="{{ route('riwayat.saya') }}">Lihat riwayat lengkap &rarr;</a></p>
        </div>
    </div>
</x-app-layout>
