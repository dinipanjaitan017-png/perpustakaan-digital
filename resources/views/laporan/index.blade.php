<x-app-layout>
    <x-slot name="header">
        <h2 class="judul-halaman">Laporan Perpustakaan</h2>
    </x-slot>

    <div class="bagian">
        <div class="container tumpuk">
            <div class="grid-stat lima">
                @foreach ([['Total', $total], ['Diajukan', $diajukan], ['Dipinjam', $dipinjam], ['Dikembalikan', $dikembalikan], ['Ditolak', $ditolak]] as [$label, $nilai])
                    <div class="kartu">
                        <div class="stat-label">{{ $label }}</div>
                        <div class="stat-nilai">{{ $nilai }}</div>
                    </div>
                @endforeach
            </div>

            <div class="kartu tabel-scroll">
                <table class="tabel">
                    <thead>
                        <tr><th>Buku</th><th>Kategori</th><th>Jumlah Dipinjam</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($perBuku as $b)
                            <tr>
                                <td>{{ $b->judul }}</td>
                                <td>{{ $b->kategori->nama ?? '-' }}</td>
                                <td>{{ $b->peminjamans_count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="kartu tabel-scroll">
                <table class="tabel">
                    <thead>
                        <tr><th>Kategori</th><th>Jumlah Buku</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($perKategori as $k)
                            <tr><td>{{ $k->nama }}</td><td>{{ $k->bukus_count }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
