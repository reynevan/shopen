<?php

namespace Shopen\Http\Requests\Admin\Order\Invoice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Shopen\Enums\Order\OrderStatus;

class CreateInvoiceCorrectionRequest extends FormRequest
{

    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'number' => ['unique:invoices,invoice_number'],
            'correction_reason' => ['required', 'string'],
            'payment_due_date' => ['required', 'date'],
            'payment_method' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'number.unique' => 'Faktura z takim numerem już istnieje.',
            'correction_reason' => 'Podaj powód korekty.',
            'payment_due_date' => 'Wybierz termin płatności',
            'payment_method' => 'Podaj metodę płatności',
        ];
    }
}
