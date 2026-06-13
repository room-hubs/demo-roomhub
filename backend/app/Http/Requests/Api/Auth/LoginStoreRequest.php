<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower(trim($this->input('email', ''))),
        ]);
    }

    public function rules(): array
    {
        return [
            'email'    => 'required|email:rfc|max:255',
            'password' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'    => 'Email is required.',
            'email.email'       => 'Please provide a valid email.',
            'password.required' => 'Password is required.',
        ];
    }
}
