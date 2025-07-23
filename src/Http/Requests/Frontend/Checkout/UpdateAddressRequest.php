<?php

namespace Shopen\Http\Requests\Frontend\Checkout;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['nullable', 'integer'],
            'first_name' => ['required'],
            'last_name' => ['required'],
            'email' => ['email', 'nullable'],
            'company' => ['nullable'],
            'address_line' => ['required'],
            'city' => ['required'],
            'postal_code' => ['required'],
            'country' => ['nullable'],
            'phone' => ['required'],
            'is_default' => ['boolean', 'nullable'],
        ];
    }
}