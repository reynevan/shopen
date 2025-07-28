<?php

namespace Shopen\Http\Requests\Frontend\Product\Review;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateProductReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->route('review') && $this->route('review')->user_id === Auth::id();
    }

    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string']
        ];
    }
}