<?php

namespace App\Models;

use Database\Factories\MediaFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Media extends Model
{
    /** @use HasFactory<MediaFactory> */
    use HasFactory;

    protected $table = 'media';

    protected $fillable = [
        'title',
        'category',
        'genre',
        'description',
        'episodes',
        'format',
    ];

    protected function casts(): array
    {
        return [
            'episodes' => 'integer',
        ];
    }

    /**
     * Relasi many-to-many: satu Media dapat dikoleksi oleh banyak User.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'media_user', 'media_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * Query scope untuk memfilter media berdasarkan judul.
     */
    public function scopeSearchTitle(Builder $query, ?string $title): Builder
    {
        return $query->when($title, function (Builder $q, string $keyword) {
            $q->where('title', 'like', "%{$keyword}%");
        });
    }

    /**
     * Query scope untuk memfilter media berdasarkan kategori.
     */
    public function scopeByCategory(Builder $query, ?string $category): Builder
    {
        return $query->when($category, function (Builder $q, string $cat) {
            $q->where('category', $cat);
        });
    }
}
