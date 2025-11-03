<?php

namespace Shopen\Http\Requests\Admin\ContactMessage;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Shopen\Enums\Order\OrderStatus;

class RespondContactMessageRequest extends FormRequest
{

    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'message' => ['required', 'string']
        ];
    }
}
