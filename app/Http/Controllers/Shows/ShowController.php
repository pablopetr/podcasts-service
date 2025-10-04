<?php

namespace App\Http\Controllers\Shows;

use App\Http\Controllers\Controller;
use App\Http\Resources\ShowResource;
use App\Models\Show;
use Illuminate\Http\Request;

class ShowController extends Controller
{
    public function __invoke(Show $show): ShowResource
    {
        return new ShowResource($show);
    }
}
