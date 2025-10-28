<?php

namespace Shopen\Http\Requests\Frontend\User\Address;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateBillingAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->route('address') && $this->route('address')->user_id === Auth::id();
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required'],
            'last_name' => ['required'],
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