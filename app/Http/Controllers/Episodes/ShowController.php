<?php

namespace App\Http\Controllers\Episodes;

use App\Http\Controllers\Controller;
use App\Http\Resources\EpisodeResource;
use App\Models\Episode;

class ShowController extends Controller
{
    public function __invoke(Episode $episode): EpisodeResource
    {
        return new EpisodeResource($episode);
    }
}
