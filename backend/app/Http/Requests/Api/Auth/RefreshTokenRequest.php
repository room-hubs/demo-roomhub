<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RefreshTokenRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'refresh_token' => ['required', 'string', 'min:64', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'refresh_token.required' => 'Refresh token is required.',
            'refresh_token.min'      => 'Invalid refresh token format.',
        ];
    }
}