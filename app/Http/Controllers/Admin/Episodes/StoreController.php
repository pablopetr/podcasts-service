<?php

namespace App\Http\Controllers\Admin\Episodes;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Episodes\StoreEpisodeRequest;
use App\Http\Resources\EpisodeResource;
use App\Models\Episode;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function __invoke(StoreEpisodeRequest $request): EpisodeResource
    {
        $data = $request->validated();

        $episode = Episode::create($data);

        return new EpisodeResource($episode);
    }
}
