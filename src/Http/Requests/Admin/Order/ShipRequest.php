<?php

namespace Shopen\Http\Requests\Admin\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Shopen\Enums\Order\OrderStatus;

class ShipRequest extends FormRequest
{

    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'shipping_tracking_code' => ['nullable', 'string']
        ];
    }
}
