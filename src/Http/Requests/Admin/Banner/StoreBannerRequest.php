<?php

namespace Shopen\Http\Requests\Admin\Banner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Shopen\Enums\Banner\PlacementType;
use Shopen\Enums\Promocode\ApplyType;
use Shopen\Enums\Promocode\DiscountType;

class StoreBannerRequest extends FormRequest
{

    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->isAdmin();
    }

    public function rules(): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'alt_text' => ['required', 'string', 'max:255'],
            'image_desktop' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:10000'],
            'image_mobile' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:10000'],
            'link_url' => ['nullable', 'string'],
            'opens_in_new_tab' => ['boolean'],
            'placement_type' => ['required', Rule::enum(PlacementType::class)],
            'placement_key' => ['required', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_active' => ['boolean'],
            'sort_order' => ['required', 'integer'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['exists:categories,id'],
        ];

        // Zasady dla edycji (PUT/PATCH)
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            // Obrazek nie jest wymagany podczas edycji
            $rules['image_desktop'] = ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:10000'];
        }

        return $rules;
    }
}
