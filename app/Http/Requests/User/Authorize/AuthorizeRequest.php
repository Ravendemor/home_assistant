<?php

namespace App\Http\Requests\User\Authorize;

use Illuminate\Foundation\Http\FormRequest;

class AuthorizeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:32']
        ];
    }
}
