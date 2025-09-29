<?php

namespace Shopen\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Shopen\Enums\Promocode\ApplyType;
use Shopen\Enums\Promocode\DiscountType;

class StoreProductRequest extends FormRequest
{

    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'type' => 'required',
            'sku' => 'required|unique:products,sku',
            'url_key' => 'required|unique:url_rewrites,request_path',
            'price.price' => 'required|numeric',
            'attributes.name' => 'required',
            'cross_sell_ids'   => 'nullable|array',
            'cross_sell_ids.*' => 'exists:products,id',
            'up_sell_ids'      => 'nullable|array',
            'up_sell_ids.*'    => 'exists:products,id',
            'related_ids'      => 'nullable|array',
            'related_ids.*'    => 'exists:products,id',
        ];
    }
}
