<?php

namespace App\Http\Controllers\Admin\Shows;

use App\Http\Controllers\Controller;
use App\Models\Show;
use Illuminate\Http\Request;

class DestroyController extends Controller
{
    public function __invoke(Show $show)
    {
        $show->delete();

        return response()->noContent();
    }
}
