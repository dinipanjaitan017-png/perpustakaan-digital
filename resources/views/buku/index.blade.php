<x-app-layout>
    <x-slot name="header">
        <h2 class="judul-halaman">Daftar Buku</h2>
    </x-slot>

    <section class="bagian">
        <div class="container">
            @can('kelola data buku')
                <p><a href="{{ route('buku.kelola') }}" class="btn btn-secondary">Kelola data buku</a></p>
            @endcan

            @if ($bukus->isEmpty())
                <div class="kartu kosong">Belum ada data buku.</div>
            @else
                <div class="book-grid">
                    @foreach ($bukus as $buku)
                        <x-book-card
                            :id="$buku->id"
                            :judul="$buku->judul"
                            :penulis="$buku->penulis"
                            :tahun="$buku->tahun_terbit"
                            :kategori="$buku->kategori->nama ?? 'Umum'"
                        >
                            @can('mengajukan peminjaman buku')
                                <x-slot name="description">
                                    <form method="POST" action="{{ route('peminjaman.ajukan') }}">
                                        @csrf
                                        <input type="hidden" name="buku_id" value="{{ $buku->id }}">
                                        <button class="btn btn-primary">Ajukan Pinjam</button>
                                    </form>
                                </x-slot>
                            @endcan
                        </x-book-card>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
</x-app-layout>
