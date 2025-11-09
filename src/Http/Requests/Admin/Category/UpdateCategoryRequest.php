<?php

namespace Shopen\Http\Requests\Admin\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateCategoryRequest extends FormRequest
{

    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'is_active' => 'required|boolean',
            'is_canonical' => 'boolean',
            'attributes' => 'required|array',
            'remove_image_menu' => 'nullable|boolean',
            'parent_id' => 'nullable|integer|exists:categories,id',
        ];
    }
}
