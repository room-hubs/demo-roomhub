<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token'    => 'required|string',
            'email'    => 'required|email|exists:users,email',
            'password' => 'required|min:6|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'token.required'     => 'Reset token is required.',
            'email.required'     => 'Email is required.',
            'email.exists'       => 'No account found with this email.',
            'password.required'  => 'Password is required.',
            'password.min'       => 'Password must be at least 6 characters.',
            'password.confirmed' => 'Passwords do not match.',
        ];
    }
}
