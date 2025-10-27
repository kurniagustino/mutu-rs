<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class MigrasiUsersLama extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrasi-users-lama';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrasi data users_old/pegawai_old ke users (Reset Password = Username Baru)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai migrasi data users (Reset Password)...');
        Schema::disableForeignKeyConstraints();
        DB::table('users')->truncate();
        $this->info('Tabel users baru dikosongkan.');

        $usersLama = DB::table('users_old')
            ->leftJoin('pegawai_old', 'users_old.id_pegawai', '=', 'pegawai_old.id_pegawai')
            ->select(
                'users_old.id_admin',
                'users_old.user',
                'users_old.pass',
                'users_old.email as email_lama',
                'users_old.level',
                'users_old.identitas',
                'users_old.aktivasi',
                'users_old.status',
                'users_old.fto',
                'users_old.created',
                'users_old.updated_db',
                'pegawai_old.nama',
                'pegawai_old.nip_nrp',
                'pegawai_old.tgllahir',
                'pegawai_old.tempatlahir',
                'pegawai_old.glr_depan',
                'pegawai_old.glr_blkg',
                'pegawai_old.alamat',
                'pegawai_old.pendidikan_terakhir'
            )
            ->orderBy('users_old.id_admin')
            ->get();

        $this->info('Mengambil '.$usersLama->count().' data lama untuk ditransformasi...');

        $dataBaru = [];
        $emailUnik = [];
        $usernameUnik = [];

        foreach ($usersLama as $user) {

            // --- Transformasi Data ---
            $namaBaru = $user->nama ?? $user->user;
            $createdAt = $user->created ?? now();
            $updatedAt = $user->updated_db ?? now();

            // 1. Cek Username Unik
            $usernameBaru = $user->user;
            if (isset($usernameUnik[$usernameBaru])) {
                $usernameBaru = $user->user.'_'.$user->id_admin;
                $this->warn("   -> Username '{$user->user}' duplikat. Menjadi '{$usernameBaru}'.");
            }
            $usernameUnik[$usernameBaru] = true;

            // 2. BUAT PASSWORD BCRYPT BARU (RESET)
            // Password baru = username baru
            $passwordBaru = Hash::make($usernameBaru);

            // 3. Cek Email Unik
            $emailBaru = $user->email_lama;
            if (empty($emailBaru)) {
                $emailBaru = $usernameBaru.'@example.com';
            }
            if (isset($emailUnik[$emailBaru])) {
                $emailBaru = $usernameBaru.'@example.com';
            }
            $emailUnik[$emailBaru] = true;

            // 4. Susun data baru
            $dataBaru[] = [
                'id' => $user->id_admin,
                'name' => $namaBaru,
                'username' => $usernameBaru, // Username baru (unik)
                'email' => $emailBaru,
                'password' => $passwordBaru, // Password baru (Bcrypt)
                'NIP' => $user->nip_nrp,
                'level' => $user->level,
                'id_ruang' => null,
                'identitas' => $user->identitas,
                'aktivasi' => $user->aktivasi,
                'status' => $user->status,
                'fto' => $user->fto,
                'tgllahir' => $user->tgllahir,
                'tempatlahir' => $user->tempatlahir,
                'glr_depan' => $user->glr_depan,
                'glr_blkg' => $user->glr_blkg,
                'alamat' => $user->alamat,
                'pendidikan_terakhir' => $user->pendidikan_terakhir,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ];
        }

        $this->info('Memasukkan data ke tabel baru...');
        foreach ($dataBaru as $data) {
            try {
                DB::table('users')->insert($data);
            } catch (\Exception $e) {
                $this->error("Gagal memasukkan user ID: {$data['id']} ({$data['username']}). Error: ".$e->getMessage());
            }
        }

        Schema::enableForeignKeyConstraints();

        $this->info('===================================================');
        $this->info('SUKSES! Data users telah dimigrasi.');
        $this->info('PASSWORD BARU = USERNAME BARU ANDA.');
        $this->info('===================================================');

        return 0;
    }
}
