<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'role' => 'required|exists:roles,name',
        ];
    }

    public function messages(): array
    {
        return [
            'role.required' => 'Role is required.',
            'role.exists'   => 'Selected role is invalid.',
        ];
    }
}
