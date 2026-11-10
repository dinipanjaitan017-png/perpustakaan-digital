<x-app-layout>
    <x-slot name="header">
        <h2 class="judul-halaman">Kelola Pengguna & Role</h2>
    </x-slot>

    <div class="bagian">
        <div class="container tumpuk">

            <form method="POST" action="{{ route('admin.users.store') }}" class="kartu form-baris">
                @csrf
                <div><label class="label">Nama</label><input name="name" value="{{ old('name') }}" required class="input"></div>
                <div><label class="label">Email</label><input type="email" name="email" value="{{ old('email') }}" required class="input"></div>
                <div><label class="label">Password</label><input type="password" name="password" required class="input"></div>
                <div>
                    <label class="label">Role</label>
                    <select name="role" class="input">
                        @foreach ($roles as $r)
                            <option value="{{ $r }}" @selected($r === 'siswa')>{{ ucfirst($r) }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn btn-primary">Tambah Akun</button>
            </form>

            <div class="kartu tabel-scroll">
                <table class="tabel">
                    <thead>
                        <tr><th>Nama</th><th>Email</th><th>Role</th><th>Aksi</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $u)
                            <tr>
                                <td>{{ $u->name }}</td>
                                <td>{{ $u->email }}</td>
                                <td>
                                    @can('kelola role pengguna')
                                        <form method="POST" action="{{ route('admin.users.role', $u) }}" class="baris">
                                            @csrf @method('PATCH')
                                            <select name="role" class="input kecil" @disabled($u->is(auth()->user()))>
                                                @foreach ($roles as $r)
                                                    <option value="{{ $r }}" @selected($u->hasRole($r))>{{ ucfirst($r) }}</option>
                                                @endforeach
                                            </select>
                                            @unless ($u->is(auth()->user()))
                                                <button class="tautan">Simpan</button>
                                            @endunless
                                        </form>
                                    @endcan
                                </td>
                                <td>
                                    <div class="baris">
                                        <a href="{{ route('admin.users.edit', $u) }}" class="tautan">Edit</a>
                                        @unless ($u->is(auth()->user()))
                                            <form method="POST" action="{{ route('admin.users.destroy', $u) }}" onsubmit="return confirm('Hapus akun ini?')">
                                                @csrf @method('DELETE')
                                                <button class="tautan bahaya">Hapus</button>
                                            </form>
                                        @endunless
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
