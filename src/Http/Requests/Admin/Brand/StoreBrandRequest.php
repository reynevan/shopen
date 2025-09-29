<?php

namespace Shopen\Http\Requests\Admin\Brand;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreBrandRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required','string','max:255'],
            'slug' => ['nullable','string','max:255','unique:brands,slug'],
            'description' => ['nullable','string'],
            'logo' => ['nullable','image','max:2048'],
            'meta_title' => ['nullable','string','max:255'],
            'meta_description' => ['nullable','string','max:1000'],
            'is_active' => ['boolean'],
            'show_on_homepage' => ['boolean'],
        ];
    }

    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->isAdmin();
    }
}