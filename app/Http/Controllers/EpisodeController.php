<?php

namespace App\Http\Controllers;

use App\Models\Episode;
use App\Models\Show;
use Illuminate\Http\JsonResponse;

class EpisodeController extends Controller
{
    public function index(Show $show): JsonResponse
    {
        $episodes = $show->episodes()
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        return response()->json($episodes);
    }

    public function show(Episode $episode): JsonResponse
    {
        return response()->json($episode);
    }
}
