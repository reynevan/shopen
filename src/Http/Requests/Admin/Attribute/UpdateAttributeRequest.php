<?php

namespace Shopen\Http\Requests\Admin\Attribute;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Shopen\Enums\Banner\PlacementType;
use Shopen\Enums\Promocode\ApplyType;
use Shopen\Enums\Promocode\DiscountType;

class UpdateAttributeRequest extends FormRequest
{

    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', 'unique:attributes,code,' . $this->route('attribute')->id],
            'is_filterable' => ['boolean'],
            'is_searchable' => ['boolean'],
            'is_required' => ['boolean'],
            'is_visible_in_details' => ['boolean'],
            'is_used_in_list' => ['boolean'],
            'entity_type' => ['required', 'string', 'in:category,product'],
            'frontend_type' => ['required', 'string'],
            'sort_order' => ['integer', 'nullable'],
            'options' => ['array', 'nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            '*.required' => 'To pole jest wymagane',
            'code.unique' => 'Atrybut z takim kodem już istnieje',
        ];
    }
}
