<?php

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);

    $this->admin = User::where('email', 'admin@perpustakaan.test')->first();
    $this->petugas = User::where('email', 'petugas@perpustakaan.test')->first();
    $this->siswa = User::where('email', 'siswa@perpustakaan.test')->first();

    $this->kategori = Kategori::create(['nama' => 'Novel']);
    $this->buku = Buku::create([
        'kategori_id' => $this->kategori->id,
        'judul' => 'Laskar Pelangi',
        'penulis' => 'Andrea Hirata',
        'tahun_terbit' => 2005,
    ]);
});

// ---------- Soal 1: autentikasi ----------
test('registrasi menyimpan password ter-hash dan role selalu siswa', function () {
    $this->post('/register', [
        'name' => 'Murid Baru',
        'email' => 'murid@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'admin', // upaya memilih role sendiri harus diabaikan
    ])->assertRedirect(route('dashboard', absolute: false));

    $user = User::where('email', 'murid@example.com')->first();

    expect($user->password)->not->toBe('password123');
    expect(Hash::check('password123', $user->password))->toBeTrue();
    expect($user->hasRole('siswa'))->toBeTrue();
    expect($user->hasRole('admin'))->toBeFalse();
});

test('login gagal dengan kredensial tidak valid', function () {
    $this->post('/login', ['email' => 'admin@perpustakaan.test', 'password' => 'salah']);
    $this->assertGuest();
});

// ---------- Soal 2: middleware guest ----------
test('user yang sudah login tidak bisa membuka halaman login/register', function () {
    $this->actingAs($this->siswa)->get('/login')->assertRedirect();
    $this->actingAs($this->siswa)->get('/register')->assertRedirect();
});

// ---------- Soal 4: role & permission tersimpan ----------
test('3 role dan 16 permission tersimpan sesuai matriks', function () {
    expect(Role::count())->toBe(3);
    expect(Permission::count())->toBe(16);

    expect(Role::findByName('admin')->hasPermissionTo('kelola role pengguna'))->toBeTrue();
    expect(Role::findByName('petugas')->hasPermissionTo('kelola role pengguna'))->toBeFalse();
    expect(Role::findByName('petugas')->hasPermissionTo('akses dashboard petugas'))->toBeTrue();
    expect(Role::findByName('siswa')->hasPermissionTo('kelola data buku'))->toBeFalse();
    expect(Role::findByName('siswa')->hasPermissionTo('mengajukan peminjaman buku'))->toBeTrue();
});

// ---------- Skenario pengujian tabel soal 5 ----------
test('1. belum login membuka dashboard admin dialihkan ke login', function () {
    $this->get('/admin/dashboard')->assertRedirect(route('login'));
});

test('2. admin membuka dashboard admin', function () {
    $this->actingAs($this->admin)->get('/admin/dashboard')->assertOk();
});

test('3. admin menambah, mengubah, menghapus buku', function () {
    $this->actingAs($this->admin)->get('/kelola/buku')->assertOk();

    $res = $this->actingAs($this->admin)->postJson('/api/buku', [
        'kategori_id' => $this->kategori->id,
        'judul' => 'Bumi Manusia',
        'penulis' => 'Pramoedya Ananta Toer',
        'tahun_terbit' => 1980,
    ])->assertCreated();

    $id = $res->json('data.id');

    $this->actingAs($this->admin)->putJson("/api/buku/{$id}", [
        'kategori_id' => $this->kategori->id,
        'judul' => 'Bumi Manusia (Edisi Baru)',
        'penulis' => 'Pramoedya Ananta Toer',
        'tahun_terbit' => 1980,
    ])->assertOk();

    $this->actingAs($this->admin)->deleteJson("/api/buku/{$id}")->assertOk();
});

test('4. admin mengelola akun dan role pengguna', function () {
    $this->actingAs($this->admin)->get('/admin/users')->assertOk();

    $this->actingAs($this->admin)->post('/admin/users', [
        'name' => 'Staf Baru',
        'email' => 'staf@example.com',
        'password' => 'password123',
        'role' => 'petugas',
    ])->assertRedirect();

    $baru = User::where('email', 'staf@example.com')->first();
    expect($baru->hasRole('petugas'))->toBeTrue();

    $this->actingAs($this->admin)
        ->patch("/admin/users/{$baru->id}/role", ['role' => 'siswa'])->assertRedirect();
    expect($baru->fresh()->hasRole('siswa'))->toBeTrue();
});

test('5. admin mengakses peminjaman, pengembalian, dan laporan', function () {
    $this->actingAs($this->admin)->get('/kelola/peminjaman')->assertOk();
    $this->actingAs($this->admin)->get('/kelola/pengembalian')->assertOk();
    $this->actingAs($this->admin)->get('/laporan')->assertOk();
});

test('6. petugas membuka dashboard petugas', function () {
    $this->actingAs($this->petugas)->get('/petugas/dashboard')->assertOk();
    $this->actingAs($this->petugas)->get('/admin/dashboard')->assertForbidden();
});

test('7. petugas mengelola data buku', function () {
    $this->actingAs($this->petugas)->get('/kelola/buku')->assertOk();
    $this->actingAs($this->petugas)->postJson('/api/buku', [
        'kategori_id' => $this->kategori->id,
        'judul' => 'Negeri 5 Menara',
        'penulis' => 'Ahmad Fuadi',
        'tahun_terbit' => 2009,
    ])->assertCreated();
});

test('8. petugas memproses peminjaman dan pengembalian', function () {
    $pinjam = Peminjaman::create([
        'anggota_id' => $this->siswa->anggotaProfil()->id,
        'buku_id' => $this->buku->id,
        'tanggal_pinjam' => now()->toDateString(),
        'status' => 'diajukan',
    ]);

    $this->actingAs($this->petugas)->patch("/kelola/peminjaman/{$pinjam->id}/setujui")->assertRedirect();
    expect($pinjam->fresh()->status)->toBe('dipinjam');

    $this->actingAs($this->petugas)->patch("/kelola/pengembalian/{$pinjam->id}")->assertRedirect();
    expect($pinjam->fresh()->status)->toBe('dikembalikan');
    expect($pinjam->fresh()->tanggal_kembali)->not->toBeNull();
});

test('9. petugas tidak bisa mengelola akun/role pengguna', function () {
    $this->actingAs($this->petugas)->get('/admin/users')->assertForbidden();
    $this->actingAs($this->petugas)
        ->patch("/admin/users/{$this->siswa->id}/role", ['role' => 'admin'])->assertForbidden();
    expect($this->siswa->fresh()->hasRole('siswa'))->toBeTrue();
});

test('10. siswa melihat daftar dan detail buku', function () {
    $this->actingAs($this->siswa)->get('/buku')->assertOk()->assertSee('Laskar Pelangi');
    $this->actingAs($this->siswa)->get("/buku/{$this->buku->id}")->assertOk();
});

test('11. siswa melihat dan mengubah profil sendiri', function () {
    $this->actingAs($this->siswa)->get('/profile')->assertOk();
    $this->actingAs($this->siswa)->patch('/profile', [
        'name' => 'Siswa Ubah Nama',
        'email' => 'siswa@perpustakaan.test',
    ])->assertSessionHasNoErrors();
    expect($this->siswa->fresh()->name)->toBe('Siswa Ubah Nama');
});

test('12. siswa melihat riwayat peminjaman sendiri', function () {
    $anggota = $this->siswa->anggotaProfil();
    $this->actingAs($this->siswa)->get('/riwayat')->assertRedirect(route('riwayat.show', $anggota));
    $this->actingAs($this->siswa)->get("/anggota/{$anggota->id}/riwayat")->assertOk();
    $this->actingAs($this->siswa)->get('/peminjaman/saya')->assertOk();
});

test('13. siswa tidak bisa membuka dashboard admin / kelola buku', function () {
    $this->actingAs($this->siswa)->get('/admin/dashboard')->assertForbidden();
    $this->actingAs($this->siswa)->get('/kelola/buku')->assertForbidden();
    $this->actingAs($this->siswa)->postJson('/api/buku', [
        'kategori_id' => $this->kategori->id,
        'judul' => 'Coba Tambah',
        'penulis' => 'Siswa',
        'tahun_terbit' => 2020,
    ])->assertForbidden();
});

test('14. siswa tidak bisa melihat riwayat siswa lain', function () {
    $lain = User::factory()->create();
    $lain->assignRole('siswa');
    $anggotaLain = $lain->anggotaProfil();

    $this->actingAs($this->siswa)->get("/anggota/{$anggotaLain->id}/riwayat")->assertForbidden();
});

test('15. setelah logout harus login kembali', function () {
    foreach ([$this->admin, $this->petugas, $this->siswa] as $user) {
        $this->actingAs($user)->post('/logout');
        $this->assertGuest();
        $this->get('/profile')->assertRedirect(route('login'));
    }
});
