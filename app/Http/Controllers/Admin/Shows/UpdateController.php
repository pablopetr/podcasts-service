<?php

namespace App\Http\Controllers\Admin\Shows;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Shows\UpdateShowRequest;
use App\Http\Resources\ShowResource;
use App\Models\Show;
use Illuminate\Http\Request;

class UpdateController extends Controller
{
    public function __invoke(UpdateShowRequest $request, Show $show)
    {
        $data = $request->validated();

        $show->fill($data)->save();

        return new ShowResource($show->fresh());
    }
}
