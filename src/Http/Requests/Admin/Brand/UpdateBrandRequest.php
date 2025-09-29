<?php

namespace Shopen\Http\Requests\Admin\Brand;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateBrandRequest extends FormRequest
{
    public function rules(): array
    {
        $brandId = $this->route('brand')?->id ?? null;

        return [
            'name' => ['required','string','max:255'],
            'slug' => [
                'nullable','string','max:255',
                Rule::unique('brands','slug')->ignore($brandId),
            ],
            'description' => ['nullable','string'],
            'logo' => ['nullable','image','max:2048'],
            'meta_title' => ['nullable','string','max:255'],
            'meta_description' => ['nullable','string','max:1000'],
            'meta_keywords' => ['nullable','string','max:1000'],
            'is_active' => ['boolean'],
            'show_on_homepage' => ['boolean'],
        ];
    }

    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->isAdmin();
    }
}