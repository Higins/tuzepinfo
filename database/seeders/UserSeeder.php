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
        // Admin felhasználó létrehozása
        User::create([
            'name' => 'Admin',
            'email' => 'admin@tuzepinfo.hu',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Teszt felhasználók létrehozása
        User::factory(5)->create();
    }
}
