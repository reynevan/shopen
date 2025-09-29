<?php

namespace Shopen\Http\Requests\Admin\TaxClass;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Shopen\Enums\Promocode\ApplyType;
use Shopen\Enums\Promocode\DiscountType;

class StoreTaxClassRequest extends FormRequest
{

    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string'],
            'name' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'rate' => ['required', 'numeric'],
        ];
    }
}
