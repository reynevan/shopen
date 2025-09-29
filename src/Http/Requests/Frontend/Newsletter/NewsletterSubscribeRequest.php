<?php

namespace Shopen\Http\Requests\Frontend\Newsletter;

use Illuminate\Foundation\Http\FormRequest;

class NewsletterSubscribeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email:rfc,dns', 'max:255'],
            'attributes' => ['sometimes', 'array'],
            'attributes.FNAME' => ['sometimes', 'string', 'max:50'],
            'attributes.LNAME' => ['sometimes', 'string', 'max:50'],
            'privacy_accepted' => ['required', 'accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Adres e-mail jest wymagany.',
            'email.email' => 'Podaj prawidłowy adres e-mail.',
            'privacy_accepted.accepted' => 'Musisz zaakceptować politykę prywatności.',
        ];
    }
}