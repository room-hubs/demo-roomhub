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
            'login' => strtolower(trim($this->input('login', ''))),
        ]);
    }

    public function rules(): array
    {
        return [
            'login'    => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'device_name' => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'login.required'    => 'Email or phone is required.',
            'password.required' => 'Password is required.',
        ];
    }
}
