<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ForgotPasswordUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Match the field name used by your controller/service:
            // controller expects "reset_token" in current implementation
            'reset_token'          => ['required', 'string'],

            // phone validation requires propaganistas/laravel-phone package
            // use E.164 to ensure consistent DB lookup (e.g. +15551234567)
            'phone'                => ['required', 'phone:E.164', 'exists:users,phone'],

            // password + confirmation (use Laravel Password rule for better policy)
            'password'             => ['required', 'string', 'confirmed', Password::min(8)],
            'password_confirmation'=> ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'reset_token.required' => 'Reset token is required.',
            'phone.required'       => 'Phone number is required.',
            'phone.phone'          => 'Please provide a valid phone number in E.164 format, e.g. +15551234567.',
            'phone.exists'         => 'No account found with this phone number.',
            'password.required'    => 'Password is required.',
            'password.min'         => 'Password must be at least :min characters.',
            'password.confirmed'   => 'Passwords do not match.',
        ];
    }
}
