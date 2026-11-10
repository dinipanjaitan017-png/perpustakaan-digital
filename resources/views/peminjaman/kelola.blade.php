<x-app-layout>
    <x-slot name="header">
        <h2 class="judul-halaman">Kelola Peminjaman</h2>
    </x-slot>

    <div class="bagian">
        <div class="container">
            <div class="kartu tabel-scroll">
                <table class="tabel">
                    <thead>
                        <tr>
                            <th>Peminjam</th><th>Buku</th>
                            <th>Tanggal</th><th>Status</th><th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($peminjamans as $p)
                            <tr>
                                <td>{{ $p->anggota->nama ?? '-' }}</td>
                                <td>{{ $p->buku->judul ?? '-' }}</td>
                                <td>{{ $p->tanggal_pinjam }}</td>
                                <td class="kapital">{{ $p->status }}</td>
                                <td>
                                    @if ($p->status === 'diajukan')
                                        @can('memproses persetujuan peminjaman')
                                            <div class="baris">
                                                <form method="POST" action="{{ route('peminjaman.setujui', $p) }}">@csrf @method('PATCH')
                                                    <button class="tautan sukses">Setujui</button></form>
                                                <form method="POST" action="{{ route('peminjaman.tolak', $p) }}">@csrf @method('PATCH')
                                                    <button class="tautan bahaya">Tolak</button></form>
                                            </div>
                                        @endcan
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="kosong">Belum ada data peminjaman.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
