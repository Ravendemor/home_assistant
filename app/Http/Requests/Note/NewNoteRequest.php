<?php

namespace App\Http\Requests\Note;

use Illuminate\Foundation\Http\FormRequest;

class NewNoteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string'],
            'link' => ['nullable', 'string'],
            'comment' => ['nullable', 'string']
        ];
    }
}
