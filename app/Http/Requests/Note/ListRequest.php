<?php

namespace App\Http\Requests\Note;

use Illuminate\Foundation\Http\FormRequest;

class ListRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'filter' => ['nullable', 'array'],
            'sort' => ['nullable', 'array', 'required_array_keys:field,order'],
            'pagination' => ['nullable', 'array', 'required_array_keys:limit,page'],
        ];
    }
}
