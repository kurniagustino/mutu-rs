<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat User baru
        $user = User::create([
            'name' => 'Admin PMKP',
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'level' => '1', // 'level' ini tidak apa-apa ada, tapi tidak dipakai Shield
        ]);

        // // 2. Berikan role 'super_admin' ke user tersebut
        // // Ini adalah bagian yang paling PENTING
        // $user->assignRole('super_admin');
    }
}
