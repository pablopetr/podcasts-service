<?php

namespace App\Http\Controllers;

use App\Models\Episode;
use Illuminate\Http\Request;

class EpisodeController extends Controller
{
    public function index()
    {
        $episodes = Episode::with('show:id,title,slug')
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        return response()->json($episodes);
    }

    public function show($slug)
    {
        $episode = Episode::with('show:id,title,slug')
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json($episode);
    }
}
