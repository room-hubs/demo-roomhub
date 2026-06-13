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
            'role' => 'required|in:rental,landlord',
        ];
    }

    public function messages(): array
    {
        return [
            'role.required' => 'Role is required.',
            'role.in'       => 'Role must be rental or landlord.',
        ];
    }
}
