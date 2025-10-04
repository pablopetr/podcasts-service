<?php

namespace App\Http\Controllers\Admin\Shows;

use App\Http\Controllers\Controller;
use App\Http\Resources\ShowCollection;
use App\Models\Show;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function __invoke(Request $request): ShowCollection
    {
        $shows = Show::query()->paginate(10);

        return new ShowCollection($shows);
    }
}
