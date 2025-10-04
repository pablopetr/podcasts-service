<?php

namespace App\Http\Controllers\Admin\Episodes;

use App\Http\Controllers\Controller;
use App\Http\Resources\EpisodeCollection;
use App\Models\Episode;
use App\Models\Show;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function __invoke(Show $show): EpisodeCollection
    {
        $episodes = $show->episodes()
            ->paginate(10);

        return new EpisodeCollection($episodes);
    }
}
