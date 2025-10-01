<?php

namespace App\Http\Controllers\Shows;

use App\Http\Controllers\Controller;
use App\Models\Show;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function __invoke(Request $request)
    {
        $shows = Show::withCount('episodes')
            ->orderBy('title')
            ->paginate(10);

        return response()->json($shows);
    }
}
