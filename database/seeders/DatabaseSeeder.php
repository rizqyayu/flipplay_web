<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            MediaSeeder::class,
        ]);

        // Hubungkan koleksi awal sesuai simulasi Modul 3 (Media 1 & 3 untuk Pengguna FlipPlay)
        $user = User::where('email', 'pengguna@flipplay.test')->first();
        $initialMediaIds = Media::whereIn('title', ['Jujutsu Kaisen', 'Blue Lock'])
            ->pluck('id')
            ->all();

        if ($user && count($initialMediaIds) > 0) {
            $user->media()->syncWithoutDetaching($initialMediaIds);
        }
    }
}
