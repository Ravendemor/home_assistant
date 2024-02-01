<?php

namespace App\Http\Requests\Note;

use Illuminate\Foundation\Http\FormRequest;

class GetNoteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => ['required', 'integer'],
        ];
    }
}
