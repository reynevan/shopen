<?php

namespace Shopen\Http\Requests\Frontend\Contact;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'email' => ['required', 'email'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:65000'],
            'phone' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Podaj imię',
            'email.required' => 'Podaj e-mail',
            'email.email' => 'Podaj poprawny adres e-mail',
            'subject.required' => 'Podaj temat wiadomośći',
            'message.required' => 'Wiadomość nie może być pusta',
            'phone.phone' => 'Podaj prawidłowy numer telefonu',
        ];
    }
}