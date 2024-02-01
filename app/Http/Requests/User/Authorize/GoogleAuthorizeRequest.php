<?php

namespace App\Http\Requests\User\Authorize;

use Illuminate\Foundation\Http\FormRequest;

class GoogleAuthorizeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'token' => ['required', 'string']
        ];
    }
}
