<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Perpustakaan Digital')</title>

    {{-- Tailwind (dipakai komponen Breeze: form profil, dll) lalu gaya khas perpustakaan --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/perpustakaan.css') }}">
</head>

<body>

    <!-- HEADER + NAVBAR -->
    <header class="header">
        <div class="container navbar">

            <a href="{{ route('home') }}" class="brand">
                <div class="brand-icon">📚</div>
                <div>
                    <h1>Perpustakaan</h1>
                    <span>Digital Library</span>
                </div>
            </a>

            <nav class="nav-menu">
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Beranda</a>

                @auth
                    @can('akses dashboard admin')
                        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard Admin</a>
                    @endcan
                    @can('akses dashboard petugas')
                        <a href="{{ route('petugas.dashboard') }}" class="{{ request()->routeIs('petugas.dashboard') ? 'active' : '' }}">Dashboard Petugas</a>
                    @endcan
                    @can('lihat daftar buku')
                        <a href="{{ route('buku.index') }}" class="{{ request()->routeIs('buku.index', 'buku.show') ? 'active' : '' }}">Daftar Buku</a>
                    @endcan
                    @can('kelola data buku')
                        <a href="{{ route('buku.kelola') }}" class="{{ request()->routeIs('buku.kelola') ? 'active' : '' }}">Kelola Buku</a>
                    @endcan
                    @can('kelola peminjaman')
                        <a href="{{ route('peminjaman.kelola') }}" class="{{ request()->routeIs('peminjaman.kelola') ? 'active' : '' }}">Peminjaman</a>
                    @endcan
                    @can('kelola pengembalian')
                        <a href="{{ route('pengembalian.kelola') }}" class="{{ request()->routeIs('pengembalian.kelola') ? 'active' : '' }}">Pengembalian</a>
                    @endcan
                    @can('lihat laporan perpustakaan')
                        <a href="{{ route('laporan.index') }}" class="{{ request()->routeIs('laporan.index') ? 'active' : '' }}">Laporan</a>
                    @endcan
                    @can('kelola data pengguna')
                        <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">Pengguna &amp; Role</a>
                    @endcan
                    @can('melihat status peminjaman sendiri')
                        <a href="{{ route('peminjaman.saya') }}" class="{{ request()->routeIs('peminjaman.saya') ? 'active' : '' }}">Pinjaman Saya</a>
                    @endcan
                    @can('melihat riwayat peminjaman sendiri')
                        <a href="{{ route('riwayat.saya') }}" class="{{ request()->routeIs('riwayat.*') ? 'active' : '' }}">Riwayat</a>
                    @endcan
                    @can('lihat profil sendiri')
                        <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.*') ? 'active' : '' }}">Profil</a>
                    @endcan

                    <span class="nav-user">
                        {{ Auth::user()->name }}
                        <span class="badge-role">
                            @role('admin') admin @elserole('petugas') petugas @elserole('siswa') siswa @endrole
                        </span>
                    </span>

                    <form method="POST" action="{{ route('logout') }}" style="display:inline">
                        @csrf
                        <button type="submit">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'active' : '' }}">Masuk</a>
                    <a href="{{ route('register') }}" class="{{ request()->routeIs('register') ? 'active' : '' }}">Daftar</a>
                @endauth
            </nav>

        </div>
    </header>

    @isset($header)
        <div class="judul-halaman-wrap">
            <div class="container">{{ $header }}</div>
        </div>
    @endisset

    @include('layouts.flash')

    <!-- KONTEN -->
    <main>
        @isset($slot)
            {{ $slot }}
        @else
            @yield('content')
        @endisset
    </main>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="container footer-content">
            <div>
                <h3>Perpustakaan Digital</h3>
                <p>Temukan berbagai buku dan pengetahuan dalam satu tempat.</p>
            </div>
            <div class="footer-right">
                <p>&copy; {{ date('Y') }} Perpustakaan Digital</p>
                <p>Laravel 13 • PBWL Modul 5</p>
            </div>
        </div>
    </footer>

</body>
</html>
