<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Episode extends Model
{
    /** @use HasFactory<\Database\Factories\EpisodeFactory> */
    use HasFactory;

    protected $fillable = [
        'show_id',
        'title',
        'slug',
        'description',
        'duration_sec',
        'audio_url',
        'published_at',
    ];

    public function show(): BelongsTo
    {
        return $this->belongsTo(Show::class);
    }
}
