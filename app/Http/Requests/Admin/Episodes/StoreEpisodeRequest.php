<?php

namespace App\Http\Requests\Admin\Episodes;

use Illuminate\Foundation\Http\FormRequest;

class StoreEpisodeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'show_id' => ['required', 'exists:shows,id'],
            'title' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:160', 'regex:/^[a-z0-9\-]+$/', 'unique:episodes,slug'],
            'description' => ['nullable', 'string', 'max:5000'],
            'duration_sec' => ['nullable', 'integer', 'min:0'],
            'audio_url' => ['required', 'url', 'max:2048'],
            'published_at' => ['nullable', 'date'],
        ];
    }
}
