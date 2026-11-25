<?php

namespace Database\Seeders;

use App\Models\Media;
use Illuminate\Database\Seeder;

class MediaSeeder extends Seeder
{
    /**
     * Seed the media table with initial FlipPlay data.
     */
    public function run(): void
    {
        $items = [
            [
                'title' => 'Jujutsu Kaisen',
                'category' => 'Anime',
                'genre' => 'Action, Supernatural',
                'description' => 'Anime action dan supernatural.',
                'episodes' => 24,
                'format' => 'Streaming',
            ],
            [
                'title' => 'Your Name',
                'category' => 'Anime',
                'genre' => 'Drama, Romance',
                'description' => 'Anime drama dan romance.',
                'episodes' => 1,
                'format' => 'Streaming',
            ],
            [
                'title' => 'Blue Lock',
                'category' => 'Manga',
                'genre' => 'Sports',
                'description' => 'Manga bertema sepak bola.',
                'episodes' => null,
                'format' => 'Digital',
            ],
            [
                'title' => 'The Beginning After The End',
                'category' => 'Light Novel',
                'genre' => 'Fantasy, Adventure',
                'description' => 'Novel fantasi petualangan.',
                'episodes' => null,
                'format' => 'Digital',
            ],
        ];

        foreach ($items as $item) {
            Media::updateOrCreate(
                ['title' => $item['title']],
                $item
            );
        }
    }
}
