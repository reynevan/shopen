<?php

namespace Shopen\Http\Requests\Frontend\Contact;

use Illuminate\Foundation\Http\FormRequest;
use RyanChandler\LaravelCloudflareTurnstile\Rules\Turnstile;

class StoreContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $data =  [
            'name' => ['required', 'string'],
            'email' => ['required', 'email'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:65000'],
            'phone' => ['nullable', 'string'],
        ];
        if (config('services.turnstile.key')) {
            $data['token'] = ['required', new Turnstile];
        }
        return $data;
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