<x-app-layout>
    <x-slot name="header">
        <h2 class="judul-halaman">Detail Peminjaman</h2>
    </x-slot>

    <div class="bagian">
        <div class="container sempit">
            <div class="kartu detail">
                <p><span class="tebal">ID Peminjaman:</span> {{ $peminjaman->id }}</p>
                <p><span class="tebal">Buku:</span> {{ $peminjaman->buku->judul ?? '-' }}</p>
                <p><span class="tebal">Peminjam:</span> {{ $peminjaman->anggota->nama ?? '-' }}</p>
                <p><span class="tebal">Tanggal Pinjam:</span> {{ $peminjaman->tanggal_pinjam }}</p>
                <p><span class="tebal">Tanggal Kembali:</span> {{ $peminjaman->tanggal_kembali ?? '-' }}</p>
                <p><span class="tebal">Status:</span> <span class="kapital">{{ $peminjaman->status }}</span></p>

                <div class="baris">
                    <a href="{{ route('peminjaman.saya') }}" class="tautan">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
