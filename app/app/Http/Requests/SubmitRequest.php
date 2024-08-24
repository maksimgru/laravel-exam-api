<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $name
 * @property string $email
 * @property string $message
 */
class SubmitRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'   => ['required', 'string', 'min:1'],
            'email'   => ['required', 'email'],
            'message'   => ['required', 'string', 'min:1'],
        ];
    }
}
