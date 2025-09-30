<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Show extends Model
{
    /** @use HasFactory<\Database\Factories\ShowFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'image_url',
    ];

    public function episodes(): HasMany
    {
        return $this->hasMany(Episode::class);
    }
}
