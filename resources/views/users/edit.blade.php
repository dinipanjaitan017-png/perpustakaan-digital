<x-app-layout>
    <x-slot name="header">
        <h2 class="judul-halaman">Edit Pengguna</h2>
    </x-slot>

    <div class="bagian">
        <div class="container sempit">
            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="kartu form">
                @csrf @method('PATCH')
                <div><label class="label">Nama</label><input name="name" value="{{ old('name', $user->name) }}" required class="input"></div>
                <div><label class="label">Email</label><input type="email" name="email" value="{{ old('email', $user->email) }}" required class="input"></div>
                <div class="baris">
                    <button class="btn btn-primary">Simpan</button>
                    <a href="{{ route('admin.users.index') }}" class="tautan abu">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
