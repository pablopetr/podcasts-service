<?php

namespace App\Http\Controllers\Episodes;

use App\Http\Controllers\Controller;
use App\Http\Resources\EpisodeCollection;
use App\Models\Show;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function __invoke(Show $show): EpisodeCollection
    {
        $episodes = $show->episodes()
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        return new EpisodeCollection($episodes);
    }
}
