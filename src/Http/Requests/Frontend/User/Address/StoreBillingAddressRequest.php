<?php

namespace Shopen\Http\Requests\Frontend\User\Address;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreBillingAddressRequest extends FormRequest
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
            'email' => ['email', 'nullable'],
            'company' => ['nullable'],
            'address_line' => ['required'],
            'city' => ['required'],
            'postal_code' => ['required'],
            'country' => ['nullable'],
            'is_default' => ['boolean', 'nullable'],
            'type' => ['required', 'in:billing'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'Podaj imię.',
            'last_name.required' => 'Podaj nazwisko.',
            'address_line.required' => 'Podaj adres.',
            'postal_code.required' => 'Podaj kod pocztowy.',
            'city.required' => 'Podaj miasto.',
            'email.email' => 'Nieprawidłowy email.',
        ];
    }
}