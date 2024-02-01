<?php

namespace App\Http\Requests\Note;

class UpdateNoteRequest extends GetNoteRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'title' => ['nullable', 'string'],
            'link' => ['nullable', 'string'],
            'comment' => ['nullable', 'string']
        ]);
    }
}
