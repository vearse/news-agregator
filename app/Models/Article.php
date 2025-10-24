<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Article extends Model
{
    /** @use HasFactory<\Database\Factories\ArticleFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'external_id',
        'source',
        'title',
        'description',
        'content',
        'author',
        'category',
        'url',
        'image_url',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    // Scopes
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (!$search) {
            return $query;
        }

        return $query->whereFullText(['title', 'description', 'content'], $search);
    }

    public function scopeFilterBySource(Builder $query, array $sources): Builder
    {
        if (empty($sources)) {
            return $query;
        }

        return $query->whereIn('source', $sources);
    }

    public function scopeFilterByCategory(Builder $query, array $categories): Builder
    {
        if (empty($categories)) {
            return $query;
        }

        return $query->whereIn('category', $categories);
    }

    public function scopeFilterByAuthor(Builder $query, array $authors): Builder
    {
        if (empty($authors)) {
            return $query;
        }

        return $query->whereIn('author', $authors);
    }

    public function scopeFilterByDateRange(Builder $query, ?string $from, ?string $to): Builder
    {
        if ($from) {
            $query->where('published_at', '>=', Carbon::parse($from));
        }

        if ($to) {
            $query->where('published_at', '<=', Carbon::parse($to));
        }

        return $query;
    }
}