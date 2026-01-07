<?php

namespace Shopen\Http\Requests\Admin\Order\Invoice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Shopen\Enums\Order\OrderStatus;

class CreateInvoiceRequest extends FormRequest
{

    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'number' => ['unique:invoices,invoice_number']
        ];
    }

    public function messages(): array
    {
        return [
            'number.unique' => 'Faktura z takim numerem już istnieje.'
        ];
    }
}
