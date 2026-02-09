<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => 'password',
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        for ($i = 1; $i <= 50; $i++) {
            User::create([
                'name' => 'User ' . $i,
                'email' => 'user' . $i . '@admin.com',
                'password' => 'password',
                'role' => 'user',
                'email_verified_at' => now(),
            ]);
        }
    }
}
