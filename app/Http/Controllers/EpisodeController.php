<?php

namespace App\Http\Controllers;

use App\Http\Resources\EpisodeCollection;
use App\Http\Resources\EpisodeResource;
use App\Models\Episode;
use App\Models\Show;

class EpisodeController extends Controller
{
    public function index(Show $show): EpisodeCollection
    {
        $episodes = $show->episodes()
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        return new EpisodeCollection($episodes);
    }

    public function show(Episode $episode): EpisodeResource
    {
        return new EpisodeResource($episode);
    }
}
