<?php

namespace Shopen\Http\Requests\Frontend\Checkout;

use Illuminate\Foundation\Http\FormRequest;
use Shopen\Enums\Address\AddressType;

class UpdateAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'id' => ['nullable', 'integer'],
            'first_name' => ['required'],
            'last_name' => ['required'],
            'email' => ['email'],
            'company' => ['nullable'],
            'address_line' => ['required'],
            'city' => ['required'],
            'postal_code' => ['required'],
            'country' => ['nullable'],
            'phone' => ['required'],
            'is_default' => ['boolean', 'nullable'],
        ];
        if ($this->request->get('type') === AddressType::BILLING->value) {
            $rules['phone'] = ['nullable'];
            $rules['email'] = ['email', 'nullable'];
        }
        return $rules;
    }
}