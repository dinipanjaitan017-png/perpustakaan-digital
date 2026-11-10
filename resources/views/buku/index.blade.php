<x-app-layout>
    <x-slot name="header">
        <h2 class="judul-halaman">Daftar Buku</h2>
    </x-slot>

    <section class="bagian">
        <div class="container">
            @can('kelola-buku')
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
                            <x-slot name="description">
                                @can('mengajukan peminjaman buku')
                                    <form method="POST" action="{{ route('peminjaman.ajukan') }}">
                                        @csrf
                                        <input type="hidden" name="buku_id" value="{{ $buku->id }}">
                                        <button class="btn btn-primary">Ajukan Pinjam</button>
                                    </form>
                                @endcan

                                {{-- [SOAL 2] Tombol Hapus memakai form dengan @csrf + @method('DELETE')
                                     agar penghapusan hanya bisa dipicu dari form resmi aplikasi
                                     (menutup celah A3 / CSRF). --}}
                                @can('kelola-buku')
                                    <form method="POST" action="{{ route('buku.destroy', $buku->id) }}"
                                          onsubmit="return confirm('Hapus buku ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger">Hapus</button>
                                    </form>
                                @endcan
                            </x-slot>
                        </x-book-card>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
</x-app-layout>
