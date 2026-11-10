<x-app-layout>
    <x-slot name="header">
        <h2 class="judul-halaman">Kelola Pengembalian</h2>
    </x-slot>

    <div class="bagian">
        <div class="container">
            <div class="kartu tabel-scroll">
                <table class="tabel">
                    <thead>
                        <tr>
                            <th>Peminjam</th><th>Buku</th>
                            <th>Tgl Pinjam</th><th>Tgl Kembali</th><th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($peminjamans as $p)
                            <tr>
                                <td>{{ $p->anggota->nama ?? '-' }}</td>
                                <td>{{ $p->buku->judul ?? '-' }}</td>
                                <td>{{ $p->tanggal_pinjam }}</td>
                                <td>{{ $p->tanggal_kembali ?? '-' }}</td>
                                <td>
                                    @if ($p->status === 'dipinjam')
                                        @can('mencatat pengembalian buku')
                                            <form method="POST" action="{{ route('pengembalian.catat', $p) }}">@csrf @method('PATCH')
                                                <button class="tautan">Catat Pengembalian</button></form>
                                        @endcan
                                    @else
                                        Dikembalikan
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="kosong">Belum ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
