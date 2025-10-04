<?php

namespace App\Http\Controllers\Admin\Shows;

use App\Http\Controllers\Controller;
use App\Models\Show;

class DestroyController extends Controller
{
    public function __invoke(Show $show)
    {
        $show->delete();

        return response()->noContent();
    }
}
