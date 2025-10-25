<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       User::create([
        'name' => 'Admin PMKP', // <-- UBAH DI SINI
        'username' => 'admin',
        'email' => 'admin@gmail.com',
        'password' => Hash::make('password'),
        'level' => '1',
    ]);
    }
}
