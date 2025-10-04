<?php

namespace App\Http\Controllers\Admin\Shows;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Shows\StoreShowRequest;
use App\Http\Resources\ShowResource;
use App\Models\Show;
use Illuminate\Support\Str;

class StoreController extends Controller
{
    public function __invoke(StoreShowRequest $request)
    {
        $data = $request->validated();

        $slug = $data['slug'] ?? Str::slug($data['title']);

        $base = $slug;
        $i = 2;
        while (Show::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        $show = Show::create([
            'title' => $data['title'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
            'cover_url' => $data['cover_url'] ?? null,
            'status' => $data['status'] ?? 'draft',
        ]);

        return (new ShowResource($show))->response()->setStatusCode(201);
    }
}
