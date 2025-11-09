<?php

namespace Shopen\Http\Requests\Admin\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreCategoryRequest extends FormRequest
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
            'display_in_menu' => 'boolean',
            'attributes' => 'required|array',
            'attributes.*' => 'nullable',
            'attributes.name' => 'required|string',
            'remove_image_menu' => 'nullable|boolean',
            'parent_id' => 'nullable|integer|exists:categories,id',
        ];
    }
}
