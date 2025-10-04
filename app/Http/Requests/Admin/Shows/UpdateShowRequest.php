<?php

namespace App\Http\Requests\Admin\Shows;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateShowRequest extends FormRequest
{
    public function rules(): array
    {
        $showId = $this->route('show')?->id;

        return [
            'title' => ['sometimes', 'string', 'max:150'],
            'slug' => ['sometimes', 'string', 'max:160', 'regex:/^[a-z0-9\-]+$/', Rule::unique('shows', 'slug')->ignore($showId)],
            'description' => ['sometimes', 'string', 'max:5000'],
            'cover_url' => ['sometimes', 'nullable', 'url', 'max:2048'],
            'status' => ['sometimes', 'in:draft,published'],
        ];
    }
}
