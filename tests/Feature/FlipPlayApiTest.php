<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlipPlayApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_and_filter_media_with_users(): void
    {
        $user = User::factory()->create(['name' => 'Pengguna FlipPlay']);
        $anime = Media::factory()->create(['title' => 'Jujutsu Kaisen', 'category' => 'Anime']);
        Media::factory()->create(['title' => 'Blue Lock', 'category' => 'Manga']);
        $user->media()->attach($anime->id);

        $response = $this->getJson('/api/media');
        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');

        $filtered = $this->getJson('/api/media?title=Jujutsu');
        $filtered->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Jujutsu Kaisen')
            ->assertJsonPath('data.0.users.0.name', 'Pengguna FlipPlay');
    }

    public function test_can_show_media_detail_and_return_404_when_missing(): void
    {
        $user = User::factory()->create(['name' => 'Rizqy Ayu']);
        $media = Media::factory()->create(['title' => 'Your Name']);
        $user->media()->attach($media->id);

        $this->getJson("/api/media/{$media->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.title', 'Your Name')
            ->assertJsonPath('data.users.0.name', 'Rizqy Ayu');

        $this->getJson('/api/media/9999')
            ->assertStatus(404);
    }

    public function test_can_create_media_and_reject_invalid_or_duplicate_input(): void
    {
        $payload = [
            'title' => 'Solo Leveling',
            'category' => 'Anime',
            'genre' => 'Action, Fantasy',
            'description' => 'Adaptasi web novel populer.',
            'episodes' => 12,
            'format' => 'Streaming',
        ];

        $this->postJson('/api/media', $payload)
            ->assertStatus(201)
            ->assertJsonPath('data.title', 'Solo Leveling');

        // Failure path: judul kosong -> 422
        $this->postJson('/api/media', [
            'title' => '',
            'category' => 'Anime',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['title']);

        // Failure path: judul duplikat -> 422
        $this->postJson('/api/media', $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    public function test_can_update_and_delete_media(): void
    {
        $media = Media::factory()->create([
            'title' => 'Blue Lock',
            'category' => 'Manga',
            'episodes' => null,
        ]);

        $this->patchJson("/api/media/{$media->id}", [
            'title' => 'Blue Lock',
            'genre' => 'Sports, Psychological',
        ])->assertStatus(200)
            ->assertJsonPath('data.genre', 'Sports, Psychological');

        $this->patchJson("/api/media/{$media->id}", [
            'title' => '',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['title']);

        $this->deleteJson("/api/media/{$media->id}")
            ->assertStatus(200)
            ->assertJson(['message' => 'Media berhasil dihapus']);

        $this->assertDatabaseMissing('media', ['id' => $media->id]);
    }

    public function test_user_collection_attach_and_detach_custom_actions(): void
    {
        $user = User::factory()->create(['name' => 'Pengguna FlipPlay']);
        $media = Media::factory()->create(['title' => 'Frieren']);

        // Happy path: attach media ke koleksi
        $this->postJson("/api/users/{$user->id}/collection", [
            'media_id' => $media->id,
        ])->assertStatus(201)
            ->assertJsonPath('message', 'Media berhasil ditambahkan ke koleksi');

        // Failure path: duplikat di koleksi -> 409 Conflict
        $this->postJson("/api/users/{$user->id}/collection", [
            'media_id' => $media->id,
        ])->assertStatus(409);

        // Failure path: media_id tidak ada -> 422
        $this->postJson("/api/users/{$user->id}/collection", [
            'media_id' => 9999,
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['media_id']);

        // Happy path: tampilkan koleksi pengguna
        $this->getJson("/api/users/{$user->id}/collection")
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Frieren');

        // Happy path: detach media dari koleksi
        $this->deleteJson("/api/users/{$user->id}/collection/{$media->id}")
            ->assertStatus(200)
            ->assertJson(['message' => 'Media berhasil dihapus dari koleksi']);

        // Failure path: detach media yang tidak ada di koleksi -> 404
        $this->deleteJson("/api/users/{$user->id}/collection/{$media->id}")
            ->assertStatus(404);
    }
}
