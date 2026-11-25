<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the users table with initial FlipPlay users.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Pengguna FlipPlay',
                'email' => 'pengguna@flipplay.test',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Rizqy Ayu',
                'email' => 'rizqy@flipplay.test',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
