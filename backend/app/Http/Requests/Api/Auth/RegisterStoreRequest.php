<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => $this->filled('email') ? strtolower(trim($this->input('email'))) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name'     => 'required|string|max:255',
            'email'    => 'required_without:phone|nullable|email:rfc|max:255|unique:users,email',
            'phone'    => 'required_without:email|nullable|string|max:20|regex:/^\+?[0-9]{7,15}$/|unique:users,phone',
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'              => 'Name is required.',
            'email.required_without'     => 'Email or phone number is required.',
            'email.unique'                => 'This email is already taken.',
            'phone.required_without'     => 'Email or phone number is required.',
            'phone.regex'                  => 'Please provide a valid phone number.',
            'phone.unique'                => 'This phone number is already taken.',
            'password.required'           => 'Password is required.',
            'password.confirmed'          => 'Password confirmation does not match.',
        ];
    }
}
