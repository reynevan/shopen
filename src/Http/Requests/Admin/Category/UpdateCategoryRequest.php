<?php

namespace Shopen\Http\Requests\Admin\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateCategoryRequest extends FormRequest
{

    public function authorize(): bool
    {
        return Auth::user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'is_active' => 'required|boolean',
            'attributes' => 'required|array',
            'remove_image_desktop' => 'nullable|boolean',
            'remove_image_mobile' => 'nullable|boolean',
        ];
    }
}
