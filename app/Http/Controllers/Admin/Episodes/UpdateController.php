<?php

namespace App\Http\Controllers\Admin\Episodes;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Episodes\UpdateEpisodeRequest;
use App\Http\Resources\EpisodeResource;
use App\Models\Episode;
use Illuminate\Http\Request;

class UpdateController extends Controller
{
    public function __invoke(UpdateEpisodeRequest $request, Episode $episode): EpisodeResource
    {
        $data = $request->validated();

        $episode->fill($data)->save();

        return new EpisodeResource($episode);
    }
}
