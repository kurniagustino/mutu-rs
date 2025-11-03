<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class MigrateOldUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('users')->truncate();
        Schema::enableForeignKeyConstraints();

        // Data dari pegawai.sql (key = id_pegawai)
        $pegawaiData = [
            1 => ['nip_nrp' => '12121', 'nama' => 'Norisman Novlin', 'tgllahir' => null, 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => 'S. Kom', 'alamat' => null, 'pendidikan_terakhir' => null],
            2 => ['nip_nrp' => '838383', 'nama' => 'Rudi Sartono', 'tgllahir' => null, 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => 'S. Kom', 'alamat' => null, 'pendidikan_terakhir' => null],
            3 => ['nip_nrp' => '', 'nama' => 'RITA DESLIANA', 'tgllahir' => null, 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => 'Amd. Kep', 'alamat' => null, 'pendidikan_terakhir' => null],
            4 => ['nip_nrp' => '', 'nama' => 'SERLA RAHAYU PUTRI', 'tgllahir' => null, 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => 'Am. Kep', 'alamat' => null, 'pendidikan_terakhir' => null],
            5 => ['nip_nrp' => '', 'nama' => 'SRI RAHAYU', 'tgllahir' => null, 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => 'Amd. Keb', 'alamat' => null, 'pendidikan_terakhir' => null],
            6 => ['nip_nrp' => '', 'nama' => 'VISCALIA HARDIYANTI', 'tgllahir' => null, 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => 'Amd. Keb', 'alamat' => null, 'pendidikan_terakhir' => null],
            7 => ['nip_nrp' => '', 'nama' => 'WINDA ELISA', 'tgllahir' => null, 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => null, 'alamat' => null, 'pendidikan_terakhir' => null],
            8 => ['nip_nrp' => '', 'nama' => 'ADI SAPUTRA', 'tgllahir' => null, 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => null, 'alamat' => null, 'pendidikan_terakhir' => null],
            9 => ['nip_nrp' => '', 'nama' => 'SRI SUNDARI', 'tgllahir' => null, 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => null, 'alamat' => null, 'pendidikan_terakhir' => null],
            10 => ['nip_nrp' => '', 'nama' => 'TRI ARINYIMAS', 'tgllahir' => null, 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => 'S. Farm,Apt', 'alamat' => null, 'pendidikan_terakhir' => null],
            11 => ['nip_nrp' => '', 'nama' => 'Netty', 'tgllahir' => null, 'tempatlahir' => null, 'glr_depan' => 'Ns', 'glr_blkg' => 'S. Kep', 'alamat' => null, 'pendidikan_terakhir' => null],
            12 => ['nip_nrp' => '', 'nama' => 'SYAFRIYALDI', 'tgllahir' => '2019-08-22', 'tempatlahir' => null, 'glr_depan' => 'Ns', 'glr_blkg' => 'S. Kep', 'alamat' => null, 'pendidikan_terakhir' => null],
            13 => ['nip_nrp' => '', 'nama' => 'ERLINA', 'tgllahir' => null, 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => null, 'alamat' => null, 'pendidikan_terakhir' => null],
            14 => ['nip_nrp' => '', 'nama' => 'NYIMAS NURATIKA FITRI', 'tgllahir' => null, 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => 'Amd. Keb', 'alamat' => null, 'pendidikan_terakhir' => null],
            15 => ['nip_nrp' => '', 'nama' => 'IRNANAKA SIMA DEWI', 'tgllahir' => null, 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => 'Amd. AK', 'alamat' => null, 'pendidikan_terakhir' => null],
            16 => ['nip_nrp' => '', 'nama' => 'HEPPY FARIDA', 'tgllahir' => null, 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => 'Amd. AK', 'alamat' => null, 'pendidikan_terakhir' => null],
            17 => ['nip_nrp' => '', 'nama' => 'RIAMA SIHOMBING', 'tgllahir' => null, 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => 'Amd. Kep', 'alamat' => null, 'pendidikan_terakhir' => null],
            18 => ['nip_nrp' => '', 'nama' => 'M.HAVIS', 'tgllahir' => null, 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => null, 'alamat' => null, 'pendidikan_terakhir' => null],
            19 => ['nip_nrp' => '', 'nama' => 'RUSLAN LUBIS', 'tgllahir' => null, 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => null, 'alamat' => null, 'pendidikan_terakhir' => null],
            20 => ['nip_nrp' => '', 'nama' => 'MEYLIN SYLVIANI ', 'tgllahir' => null, 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => 'S. Gz', 'alamat' => null, 'pendidikan_terakhir' => null],
            21 => ['nip_nrp' => '', 'nama' => 'YUNITA', 'tgllahir' => null, 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => null, 'alamat' => null, 'pendidikan_terakhir' => null],
            22 => ['nip_nrp' => '', 'nama' => 'LIA', 'tgllahir' => null, 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => null, 'alamat' => null, 'pendidikan_terakhir' => null],
            23 => ['nip_nrp' => '', 'nama' => 'NETTY HARAHAP', 'tgllahir' => null, 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => 'Amd. Kep', 'alamat' => null, 'pendidikan_terakhir' => null],
            24 => ['nip_nrp' => '', 'nama' => 'FITRIYANTI', 'tgllahir' => null, 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => 'Am. Kep', 'alamat' => null, 'pendidikan_terakhir' => null],
            25 => ['nip_nrp' => '', 'nama' => 'ROMAIDA', 'tgllahir' => null, 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => 'Am. Kep', 'alamat' => null, 'pendidikan_terakhir' => null],
            26 => ['nip_nrp' => '', 'nama' => 'Apri', 'tgllahir' => null, 'tempatlahir' => null, 'glr_depan' => 'Ns', 'glr_blkg' => 'S. Kep', 'alamat' => null, 'pendidikan_terakhir' => null],
            27 => ['nip_nrp' => '', 'nama' => 'AGUSTINA', 'tgllahir' => null, 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => 'S. Kep', 'alamat' => null, 'pendidikan_terakhir' => null],
            28 => ['nip_nrp' => '', 'nama' => 'Novianti Syam', 'tgllahir' => null, 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => 'Am. Kep', 'alamat' => null, 'pendidikan_terakhir' => null],
            29 => ['nip_nrp' => '', 'nama' => 'Syafrizal', 'tgllahir' => null, 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => 'Amd. Rad', 'alamat' => null, 'pendidikan_terakhir' => null],
            30 => ['nip_nrp' => '', 'nama' => 'SESMITA', 'tgllahir' => null, 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => 'Am. Kep', 'alamat' => null, 'pendidikan_terakhir' => null],
            31 => ['nip_nrp' => '', 'nama' => 'Tauvan ', 'tgllahir' => null, 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => 'Am. Kep', 'alamat' => null, 'pendidikan_terakhir' => null],
            32 => ['nip_nrp' => '101010', 'nama' => 'KIKI PUJI LESTARI', 'tgllahir' => '1987-05-24', 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => null, 'alamat' => null, 'pendidikan_terakhir' => null],
            33 => ['nip_nrp' => '923939', 'nama' => 'IQBAL', 'tgllahir' => '1995-04-14', 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => null, 'alamat' => null, 'pendidikan_terakhir' => null],
            34 => ['nip_nrp' => '191991', 'nama' => 'MEGA NOPRIDAWATI', 'tgllahir' => '1991-10-18', 'tempatlahir' => null, 'glr_depan' => 'Ns', 'glr_blkg' => 'S. Kep', 'alamat' => null, 'pendidikan_terakhir' => null],
            35 => ['nip_nrp' => '100000', 'nama' => 'Admin RS', 'tgllahir' => '2001-02-02', 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => null, 'alamat' => null, 'pendidikan_terakhir' => null],
            36 => ['nip_nrp' => '', 'nama' => 'Zaitun Rahmawati', 'tgllahir' => '1972-12-05', 'tempatlahir' => null, 'glr_depan' => 'dr', 'glr_blkg' => null, 'alamat' => null, 'pendidikan_terakhir' => null],
            37 => ['nip_nrp' => '', 'nama' => 'Muhammad Ridho', 'tgllahir' => null, 'tempatlahir' => null, 'glr_depan' => 'Ns', 'glr_blkg' => 'S. Kep', 'alamat' => null, 'pendidikan_terakhir' => null],
            38 => ['nip_nrp' => '1001010', 'nama' => 'Tri Yuniarti', 'tgllahir' => '1991-01-01', 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => null, 'alamat' => null, 'pendidikan_terakhir' => null],
            39 => ['nip_nrp' => '100', 'nama' => 'Rodiyat', 'tgllahir' => '1960-01-01', 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => null, 'alamat' => null, 'pendidikan_terakhir' => null],
            40 => ['nip_nrp' => '88888', 'nama' => 'Rara', 'tgllahir' => '1991-01-01', 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => null, 'alamat' => null, 'pendidikan_terakhir' => null],
            42 => ['nip_nrp' => '20', 'nama' => 'Ronal', 'tgllahir' => '1988-09-17', 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => null, 'alamat' => null, 'pendidikan_terakhir' => null],
            43 => ['nip_nrp' => '999991', 'nama' => 'Aries', 'tgllahir' => '1986-01-01', 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => null, 'alamat' => null, 'pendidikan_terakhir' => null],
            44 => ['nip_nrp' => '9393993', 'nama' => 'Rani', 'tgllahir' => '1980-01-01', 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => null, 'alamat' => null, 'pendidikan_terakhir' => null],
            45 => ['nip_nrp' => '10000', 'nama' => 'Rahma Agustina', 'tgllahir' => '1972-01-01', 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => null, 'alamat' => null, 'pendidikan_terakhir' => null],
            46 => ['nip_nrp' => '970920192039', 'nama' => 'HARDIANSYAH', 'tgllahir' => '1997-09-13', 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => null, 'alamat' => null, 'pendidikan_terakhir' => null],
            47 => ['nip_nrp' => '11111', 'nama' => 'jesica', 'tgllahir' => '1996-01-01', 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => null, 'alamat' => null, 'pendidikan_terakhir' => null],
            48 => ['nip_nrp' => '920920192017', 'nama' => 'lolyta', 'tgllahir' => '1992-09-01', 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => null, 'alamat' => null, 'pendidikan_terakhir' => null],
            49 => ['nip_nrp' => 'utary', 'nama' => 'Utary Puspita Sari', 'tgllahir' => '1989-03-02', 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => null, 'alamat' => null, 'pendidikan_terakhir' => null],
            50 => ['nip_nrp' => '910920192045 ', 'nama' => 'Revi Riskawati', 'tgllahir' => '1991-09-01', 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => null, 'alamat' => null, 'pendidikan_terakhir' => null],
            51 => ['nip_nrp' => '881120182020', 'nama' => 'NYIMAS HAYATI', 'tgllahir' => '1988-11-01', 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => null, 'alamat' => null, 'pendidikan_terakhir' => null],
            52 => ['nip_nrp' => '960220202029 ', 'nama' => 'FITRIYAH', 'tgllahir' => '1996-02-01', 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => null, 'alamat' => null, 'pendidikan_terakhir' => null],
            53 => ['nip_nrp' => '951120192074', 'nama' => 'MELANI', 'tgllahir' => '1995-11-01', 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => null, 'alamat' => null, 'pendidikan_terakhir' => null],
            54 => ['nip_nrp' => '900520152022 ', 'nama' => 'Rany Amelia L', 'tgllahir' => '1990-05-01', 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => null, 'alamat' => null, 'pendidikan_terakhir' => null],
            55 => ['nip_nrp' => '00', 'nama' => 'FITRIA', 'tgllahir' => '2020-01-01', 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => null, 'alamat' => null, 'pendidikan_terakhir' => null],
            56 => ['nip_nrp' => '00', 'nama' => 'SURYANI', 'tgllahir' => '2021-01-01', 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => null, 'alamat' => null, 'pendidikan_terakhir' => null],
            57 => ['nip_nrp' => '11', 'nama' => 'anggreani', 'tgllahir' => '2021-01-01', 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => null, 'alamat' => null, 'pendidikan_terakhir' => null],
            58 => ['nip_nrp' => '009', 'nama' => 'vivi', 'tgllahir' => '2021-02-02', 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => null, 'alamat' => null, 'pendidikan_terakhir' => null],
            59 => ['nip_nrp' => '00', 'nama' => 'RITA MIARSIH', 'tgllahir' => '2021-01-10', 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => null, 'alamat' => null, 'pendidikan_terakhir' => null],
            60 => ['nip_nrp' => '09090', 'nama' => 'vivi', 'tgllahir' => '1992-09-01', 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => null, 'alamat' => null, 'pendidikan_terakhir' => null],
            61 => ['nip_nrp' => '09090', 'nama' => 'febry', 'tgllahir' => '1992-09-01', 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => null, 'alamat' => null, 'pendidikan_terakhir' => null],
            62 => ['nip_nrp' => '1992', 'nama' => 'Radna Vilusa', 'tgllahir' => '1992-10-01', 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => null, 'alamat' => null, 'pendidikan_terakhir' => null],
            63 => ['nip_nrp' => '11111', 'nama' => 'dr. Sri Putri Handayani', 'tgllahir' => '1990-01-01', 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => null, 'alamat' => null, 'pendidikan_terakhir' => null],
            64 => ['nip_nrp' => '-', 'nama' => 'Wiwin Widiastuti', 'tgllahir' => '1995-01-01', 'tempatlahir' => null, 'glr_depan' => null, 'glr_blkg' => null, 'alamat' => null, 'pendidikan_terakhir' => null],
        ];

        // 3. Data dari users.sql (sudah saya ekstrak)
        $oldUsersData = [
            ['id_admin' => 1, 'id_pegawai' => 1, 'user' => 'promoter', 'nama' => 'Norisman Novlin', 'identitas' => '', 'email' => '', 'level' => 1, 'aktivasi' => 1, 'status' => 1, 'created' => '2018-08-29 10:29:35', 'updated_db' => '2019-04-10 22:56:07'],
            ['id_admin' => 3, 'id_pegawai' => 2, 'user' => 'humas', 'nama' => 'Rudi Sartono', 'identitas' => '', 'email' => '', 'level' => 1, 'aktivasi' => 1, 'status' => 1, 'created' => '2018-08-29 10:27:57', 'updated_db' => '2019-04-09 07:08:13'],
            ['id_admin' => 84, 'id_pegawai' => 3, 'user' => 'rita', 'nama' => 'RITA DESLIANA', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2019-04-09 00:00:00', 'updated_db' => '2019-04-09 07:50:24'],
            ['id_admin' => 85, 'id_pegawai' => 4, 'user' => 'serla', 'nama' => 'SERLA RAHAYU PUTRI', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2019-04-09 00:00:00', 'updated_db' => '2019-04-09 07:50:51'],
            ['id_admin' => 86, 'id_pegawai' => 5, 'user' => 'srirahayu', 'nama' => 'SRI RAHAYU', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2019-04-09 00:00:00', 'updated_db' => '2019-04-09 20:17:31'],
            ['id_admin' => 87, 'id_pegawai' => 6, 'user' => 'viscalia', 'nama' => 'VISCALIA HARDIYANTI', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2019-04-09 00:00:00', 'updated_db' => '2019-04-09 07:51:13'],
            ['id_admin' => 88, 'id_pegawai' => 7, 'user' => 'winda', 'nama' => 'WINDA ELISA', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2019-04-09 00:00:00', 'updated_db' => '2019-04-09 07:51:23'],
            ['id_admin' => 89, 'id_pegawai' => 8, 'user' => 'adi', 'nama' => 'ADI SAPUTRA', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2019-04-09 00:00:00', 'updated_db' => '2019-08-29 08:48:54'],
            ['id_admin' => 90, 'id_pegawai' => 9, 'user' => 'srisundari', 'nama' => 'SRI SUNDARI', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2019-04-09 00:00:00', 'updated_db' => '2019-04-09 22:02:18'],
            ['id_admin' => 91, 'id_pegawai' => 10, 'user' => 'tri', 'nama' => 'TRI ARINYIMAS', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2019-04-09 00:00:00', 'updated_db' => '2020-06-23 02:29:05'],
            ['id_admin' => 92, 'id_pegawai' => 11, 'user' => 'netty', 'nama' => 'EVI', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2019-04-09 00:00:00', 'updated_db' => '2025-06-07 01:52:06'],
            ['id_admin' => 93, 'id_pegawai' => 12, 'user' => 'sidik', 'nama' => 'SIDIK', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2019-04-09 00:00:00', 'updated_db' => '2022-06-07 02:29:58'],
            ['id_admin' => 94, 'id_pegawai' => 13, 'user' => 'erlina', 'nama' => 'ERLINA', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2019-04-09 00:00:00', 'updated_db' => '2019-04-09 08:09:28'],
            ['id_admin' => 95, 'id_pegawai' => 14, 'user' => 'nyimas', 'nama' => 'NYIMAS NURATIKA FITRI', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2019-04-09 00:00:00', 'updated_db' => '2019-04-09 08:09:43'],
            ['id_admin' => 96, 'id_pegawai' => 15, 'user' => 'irnanaka', 'nama' => 'IRNANAKA SIMA DEWI', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2019-04-09 00:00:00', 'updated_db' => '2019-04-09 08:10:02'],
            ['id_admin' => 97, 'id_pegawai' => 16, 'user' => 'heppy', 'nama' => 'HEPPY FARIDA', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2019-04-09 00:00:00', 'updated_db' => '2019-04-09 08:10:19'],
            ['id_admin' => 98, 'id_pegawai' => 17, 'user' => 'riama', 'nama' => 'RIAMA SIHOMBING', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2019-04-09 00:00:00', 'updated_db' => '2019-04-09 08:10:31'],
            ['id_admin' => 99, 'id_pegawai' => 18, 'user' => 'feby', 'nama' => 'Feby', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2019-04-09 00:00:00', 'updated_db' => '2022-10-17 21:56:02'],
            ['id_admin' => 100, 'id_pegawai' => 19, 'user' => 'ruslan', 'nama' => 'RUSLAN LUBIS', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2019-04-09 00:00:00', 'updated_db' => '2019-04-09 08:10:53'],
            ['id_admin' => 101, 'id_pegawai' => 20, 'user' => 'meylin', 'nama' => 'MEYLIN SYLVIANI ', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2019-04-09 00:00:00', 'updated_db' => '2021-12-15 05:44:33'],
            ['id_admin' => 102, 'id_pegawai' => 21, 'user' => 'yunita', 'nama' => 'YUNITA', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2019-04-09 00:00:00', 'updated_db' => '2019-04-09 08:11:15'],
            ['id_admin' => 103, 'id_pegawai' => 22, 'user' => 'lia', 'nama' => 'LIA', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2019-04-09 00:00:00', 'updated_db' => '2019-04-09 08:11:51'],
            ['id_admin' => 104, 'id_pegawai' => 23, 'user' => 'netty', 'nama' => 'NETTY HARAHAP', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2019-04-09 00:00:00', 'updated_db' => '2019-04-11 01:08:02'],
            ['id_admin' => 105, 'id_pegawai' => 24, 'user' => 'fitriyanti', 'nama' => 'FITRIYANTI', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2019-04-09 00:00:00', 'updated_db' => '2022-07-07 03:02:08'],
            ['id_admin' => 106, 'id_pegawai' => 25, 'user' => 'romaida', 'nama' => 'ROMAIDA', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2019-04-09 00:00:00', 'updated_db' => '2019-04-09 08:12:38'],
            ['id_admin' => 107, 'id_pegawai' => 26, 'user' => 'apri', 'nama' => 'Apri', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2019-04-09 00:00:00', 'updated_db' => '2019-05-01 23:49:34'],
            ['id_admin' => 108, 'id_pegawai' => 27, 'user' => 'agustina', 'nama' => 'AGUSTINA', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2019-04-09 00:00:00', 'updated_db' => '2019-04-09 08:13:06'],
            ['id_admin' => 109, 'id_pegawai' => 28, 'user' => 'novianti', 'nama' => 'Novianti Syam', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2019-04-09 00:00:00', 'updated_db' => '2025-07-24 05:11:51'],
            ['id_admin' => 110, 'id_pegawai' => 29, 'user' => 'syafrizal', 'nama' => 'Syafrizal', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2019-04-09 00:00:00', 'updated_db' => '2019-04-09 08:13:33'],
            ['id_admin' => 111, 'id_pegawai' => 30, 'user' => 'sesmita', 'nama' => 'SESMITA', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2019-04-09 00:00:00', 'updated_db' => '2019-04-09 08:14:01'],
            ['id_admin' => 112, 'id_pegawai' => 31, 'user' => 'tauvan', 'nama' => 'Tauvan ', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2019-04-09 00:00:00', 'updated_db' => '2019-10-22 00:46:55'],
            ['id_admin' => 113, 'id_pegawai' => 32, 'user' => 'kiki', 'nama' => 'KIKI PUJI LESTARI', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2019-04-10 00:00:00', 'updated_db' => '2019-04-10 04:54:16'],
            ['id_admin' => 114, 'id_pegawai' => 33, 'user' => 'iqbal', 'nama' => 'IQBAL', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2019-04-10 00:00:00', 'updated_db' => '2019-04-10 04:27:27'],
            ['id_admin' => 115, 'id_pegawai' => 34, 'user' => 'mega', 'nama' => 'MEGA NOPRIDAWATI', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2019-04-10 00:00:00', 'updated_db' => '2019-08-22 04:56:31'],
            ['id_admin' => 116, 'id_pegawai' => 35, 'user' => 'adminrs', 'nama' => 'Admin RS', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2019-04-11 00:00:00', 'updated_db' => '2019-04-11 05:57:03'],
            ['id_admin' => 117, 'id_pegawai' => 36, 'user' => 'zaitun', 'nama' => 'Zaitun Rahmawati', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => null, 'updated_db' => '2019-04-11 08:13:58'],
            ['id_admin' => 118, 'id_pegawai' => 37, 'user' => 'ridho', 'nama' => 'Muhammad Ridho', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => null, 'updated_db' => '2019-06-28 02:00:53'],
            ['id_admin' => 119, 'id_pegawai' => 38, 'user' => 'yuni', 'nama' => 'Tri Yuniarti', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2019-04-15 00:00:00', 'updated_db' => '2019-04-15 00:24:21'],
            ['id_admin' => 120, 'id_pegawai' => 39, 'user' => 'rodiyat', 'nama' => 'Rodiyat', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2019-04-18 00:00:00', 'updated_db' => '2019-04-18 02:20:00'],
            ['id_admin' => 121, 'id_pegawai' => 40, 'user' => 'rara', 'nama' => 'Rara', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2019-04-24 00:00:00', 'updated_db' => '2019-04-24 03:43:20'],
            ['id_admin' => 122, 'id_pegawai' => 42, 'user' => 'ronal', 'nama' => 'Ronal', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2019-08-20 00:00:00', 'updated_db' => '2021-12-15 05:44:24'],
            ['id_admin' => 123, 'id_pegawai' => 43, 'user' => 'aries', 'nama' => 'Aries', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2019-08-29 00:00:00', 'updated_db' => '2019-08-29 08:45:18'],
            ['id_admin' => 124, 'id_pegawai' => 44, 'user' => 'rani', 'nama' => 'Rani', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2019-08-29 00:00:00', 'updated_db' => '2019-08-29 08:46:35'],
            ['id_admin' => 125, 'id_pegawai' => 45, 'user' => 'rahma', 'nama' => 'Rahma Agustina', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2019-10-07 00:00:00', 'updated_db' => '2019-10-07 04:40:59'],
            ['id_admin' => 126, 'id_pegawai' => 46, 'user' => 'ian', 'nama' => 'HARDIANSYAH', 'identitas' => null, 'email' => null, 'level' => 0, 'aktivasi' => 0, 'status' => 0, 'created' => '2020-02-18 00:00:00', 'updated_db' => '2020-04-15 01:16:30'],
            ['id_admin' => 127, 'id_pegawai' => 47, 'user' => 'jesica', 'nama' => 'jesica', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2020-04-15 00:00:00', 'updated_db' => '2020-04-15 01:10:44'],
            ['id_admin' => 128, 'id_pegawai' => 48, 'user' => 'lolyta', 'nama' => 'lolyta', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 0, 'status' => 0, 'created' => '2020-07-02 00:00:00', 'updated_db' => '2021-11-17 00:46:25'],
            ['id_admin' => 129, 'id_pegawai' => 49, 'user' => 'utary', 'nama' => 'utary', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2020-07-02 00:00:00', 'updated_db' => '2020-07-02 04:36:52'],
            ['id_admin' => 130, 'id_pegawai' => 50, 'user' => 'revi', 'nama' => 'Revi Riskawati', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2021-11-17 00:00:00', 'updated_db' => '2021-11-17 01:37:10'],
            ['id_admin' => 131, 'id_pegawai' => 51, 'user' => 'hayati', 'nama' => 'NYIMAS HAYATI', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2021-11-17 00:00:00', 'updated_db' => '2021-11-17 01:38:50'],
            ['id_admin' => 132, 'id_pegawai' => 52, 'user' => 'fitriyah', 'nama' => 'FITRIYAH', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2021-11-17 00:00:00', 'updated_db' => '2021-11-17 01:39:47'],
            ['id_admin' => 133, 'id_pegawai' => 53, 'user' => 'melani', 'nama' => 'MELANI', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2021-11-17 00:00:00', 'updated_db' => '2021-11-17 04:11:41'],
            ['id_admin' => 134, 'id_pegawai' => 54, 'user' => 'rany', 'nama' => 'Rany Amelia L', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2021-11-17 00:00:00', 'updated_db' => '2021-11-17 04:13:52'],
            ['id_admin' => 135, 'id_pegawai' => 55, 'user' => 'fitria', 'nama' => 'FITRIA', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2021-12-10 00:00:00', 'updated_db' => '2021-12-10 03:06:39'],
            ['id_admin' => 136, 'id_pegawai' => 56, 'user' => 'suryani', 'nama' => 'SURYANI', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2021-12-10 00:00:00', 'updated_db' => '2021-12-10 03:13:09'],
            ['id_admin' => 137, 'id_pegawai' => 57, 'user' => 'anggraeni', 'nama' => 'anggreani', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2021-12-10 00:00:00', 'updated_db' => '2021-12-10 03:14:36'],
            ['id_admin' => 138, 'id_pegawai' => 59, 'user' => 'ritarm', 'nama' => 'RITA MIARSIH', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2021-12-11 00:00:00', 'updated_db' => '2021-12-11 06:11:19'],
            ['id_admin' => 139, 'id_pegawai' => 58, 'user' => 'vivi', 'nama' => 'vivi', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2022-01-05 00:00:00', 'updated_db' => '2022-01-05 08:41:44'],
            ['id_admin' => 140, 'id_pegawai' => 61, 'user' => 'febri', 'nama' => 'febri', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2022-01-05 00:00:00', 'updated_db' => '2022-06-07 04:12:49'],
            ['id_admin' => 141, 'id_pegawai' => 62, 'user' => 'radna', 'nama' => 'Radna Vilusa', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2022-07-07 00:00:00', 'updated_db' => '2022-07-07 02:59:35'],
            ['id_admin' => 142, 'id_pegawai' => 63, 'user' => 'sriputri', 'nama' => 'dr. Sri Putri Handayani', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2022-07-12 00:00:00', 'updated_db' => '2022-07-12 08:10:39'],
            ['id_admin' => 143, 'id_pegawai' => 64, 'user' => 'wiwin', 'nama' => 'Wiwin Widiastuti', 'identitas' => null, 'email' => null, 'level' => 2, 'aktivasi' => 1, 'status' => 1, 'created' => '2024-09-23 00:00:00', 'updated_db' => '2024-09-23 03:18:06'],
        ];

        // 4. Siapkan array untuk insert
        $usersToInsert = [];
        $defaultPassword = Hash::make('password'); // Password default untuk semua
        $now = now();

        // ✅ --- PERBAIKAN: Tambahkan array pelacak ---
        $usedUsernames = [];
        $usedEmails = [];
        // ------------------------------------------

        foreach ($oldUsersData as $oldUser) {
            // Cari data pegawai yang sesuai
            $pegawai = $pegawaiData[$oldUser['id_pegawai']] ?? null;

            // Tentukan nama: prioritaskan nama dari tabel pegawai, jika tidak ada, pakai nama dari tabel users
            $nama = $pegawai['nama'] ?? $oldUser['nama'];

            // ✅ --- PERBAIKAN: Logika untuk memastikan data unik ---
            $username = $oldUser['user'];
            $email = $oldUser['email'] ?? null;

            if (empty($email)) {
                $email = $oldUser['user'].'@rumahsakit.local'; // Buat email unik palsu
            }

            // Cek duplikat username
            $originalUsername = $username;
            $counter = 1;
            while (in_array($username, $usedUsernames)) {
                $username = $originalUsername.'_'.$counter;
                $counter++;
            }
            $usedUsernames[] = $username; // Catat username unik

            // Cek duplikat email
            $originalEmail = $email;
            $counter = 1;
            while (in_array($email, $usedEmails)) {
                $emailParts = explode('@', $originalEmail);
                $email = $emailParts[0].'_'.$counter.'@'.$emailParts[1];
                $counter++;
            }
            $usedEmails[] = $email; // Catat email unik
            // ----------------------------------------------------

            $usersToInsert[] = [
                'name' => $nama,
                'username' => $username, // Gunakan username unik
                'email' => $email, // Gunakan email unik
                'password' => $defaultPassword, // Password default yang sudah di-hash
                'NIP' => $pegawai['nip_nrp'] ?? null,
                'identitas' => $oldUser['identitas'],
                'aktivasi' => $oldUser['aktivasi'],
                'status' => $oldUser['status'],
                'tgllahir' => $pegawai['tgllahir'] ?? null,
                'tempatlahir' => $pegawai['tempatlahir'] ?? null,
                'glr_depan' => $pegawai['glr_depan'] ?? null,
                'glr_blkg' => $pegawai['glr_blkg'] ?? null,
                'alamat' => $pegawai['alamat'] ?? null,
                'pendidikan_terakhir' => $pegawai['pendidikan_terakhir'] ?? null,
                'created_at' => $oldUser['created'] ?? $now,
                'updated_at' => $oldUser['updated_db'] ?? $now,
            ];
        }

        // 5. Insert semua data ke tabel 'users' baru
        // Kita pakai insert() agar bisa mem-bypass $fillable
        DB::table('users')->insert($usersToInsert);

        // 6. Atur ulang sequence auto-increment jika perlu (opsional tapi bagus)
        // $maxId = DB::table('users')->max('id') + 1;
        // DB::statement("ALTER TABLE users AUTO_INCREMENT = $maxId;");
    }
}
