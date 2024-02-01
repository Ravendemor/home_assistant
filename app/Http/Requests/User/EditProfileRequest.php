<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class EditProfileRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'login' => ['nullable', 'string'],
            'password' => ['nullable', 'string', 'min:8', 'max:32']
        ];
    }
}
