<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Validate specific to Cambodia (+855)
            // 'phone' => 'required|phone:KH|exists:users,phone',
            'phone' => ['required', 'phone:KH'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => 'Phone number is required.',
            'phone.phone'    => 'Please provide a valid Cambodian phone number.',
            'phone.exists'   => 'No account found with this phone number.',
        ];
    }
}
