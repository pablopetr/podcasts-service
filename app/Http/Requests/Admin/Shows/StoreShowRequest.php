<?php

namespace App\Http\Requests\Admin\Shows;

use Illuminate\Foundation\Http\FormRequest;

class StoreShowRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:160', 'regex:/^[a-z0-9\-]+$/', 'unique:shows,slug'],
            'description' => ['nullable', 'string', 'max:5000'],
            'cover_url' => ['nullable', 'url', 'max:2048'],
            'status' => ['nullable', 'in:draft,published'],
        ];
    }
}
