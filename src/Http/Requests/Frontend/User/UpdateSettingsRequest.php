<?php

namespace Shopen\Http\Requests\Frontend\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required'],
            'last_name' => ['required'],
            'email' => ['email', 'unique:users,email,' . Auth::id()],
            'new_password' => ['nullable', 'min:8'],
            'password' => ['required', 'current_password']
        ];
    }

    public function messages(): array
    {
        return [
            'new_password.min' => 'Hasło musi mieć przynajmniej 8 znaków.'
        ];
    }
}