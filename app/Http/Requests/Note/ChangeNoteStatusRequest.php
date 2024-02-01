<?php

namespace App\Http\Requests\Note;

use Illuminate\Foundation\Http\FormRequest;

class ChangeNoteStatusRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => ['required', 'integer'],
        ];
    }
}
