<x-app-layout>
    <x-slot name="header">
        <h2 class="judul-halaman">Detail Buku</h2>
    </x-slot>

    <div class="bagian">
        <div class="container sempit">
            <div class="kartu detail">
                <p><span class="tebal">Judul:</span> {{ $buku->judul }}</p>
                <p><span class="tebal">Penulis:</span> {{ $buku->penulis }}</p>
                <p><span class="tebal">Kategori:</span> {{ $buku->kategori->nama ?? '-' }}</p>
                <p><span class="tebal">Tahun Terbit:</span> {{ $buku->tahun_terbit }}</p>

                <div class="baris">
                    <a href="{{ route('buku.index') }}" class="tautan">Kembali</a>

                    @can('mengajukan peminjaman buku')
                        <form method="POST" action="{{ route('peminjaman.ajukan') }}">
                            @csrf
                            <input type="hidden" name="buku_id" value="{{ $buku->id }}">
                            <button class="btn btn-primary">Ajukan Peminjaman</button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
