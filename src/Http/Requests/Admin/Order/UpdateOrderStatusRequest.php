<?php

namespace Shopen\Http\Requests\Admin\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Shopen\Enums\Order\OrderStatus;

class UpdateOrderStatusRequest extends FormRequest
{

    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:' . implode(',', array_keys(OrderStatus::options()))],
            'comment' => ['nullable', 'string'],
            'email_notification' => ['nullable', 'boolean'],
        ];
    }
}
