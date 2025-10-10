<?php

namespace Shopen\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Shopen\Models\Product\Product;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->isAdmin();
    }

    public function rules(): array
    {

        $rules = [
            'visibility' => 'nullable',
            'sku' => 'required|unique:products,sku,' . $this->route('product')->id,
            'url_key' => 'nullable|unique:url_rewrites,request_path,' . $this->getRewriteId(),
            'attributes.name' => 'required',
            'stock_qty' => 'nullable|numeric|min:0',
            'cross_sell_ids' => 'nullable|array',
            'cross_sell_ids.*' => 'exists:products,id',
            'up_sell_ids' => 'nullable|array',
            'up_sell_ids.*' => 'exists:products,id',
            'related_ids' => 'nullable|array',
            'related_ids.*' => 'exists:products,id',
        ];
        if ($this->request->get('type') === Product::TYPE_SIMPLE || !$this->request->get('type') ) {
            $rules['price.price'] = 'required|numeric';
        }
        if ($parent = $this->getProductParent()) {
            foreach ($parent->configurableAttributes as $attribute) {
                $rules['attributes.' . $attribute->code] = 'required';
            }
        }
        return $rules;
    }

    public function messages(): array
    {
        $messages = [
            'attributes.name.required' => 'Nazwa produktu jest wymagana',
            'price.price.required' => 'Uzupełnij cenę'
        ];
        if ($parent = $this->getProductParent()) {
            foreach ($parent->configurableAttributes as $attribute) {
                $messages["attributes.{$attribute->code}.required"] = "Wybierz wartość atrybutu '{$attribute->name}'";
            }
        }

        return $messages;
    }

    protected function getProductParent()
    {
        if ($parentId = $this->request->get('parent_id')) {
            $parent = Product::query()->find($parentId);
            return $parent;
        }
        return null;
    }

    protected function getRewriteId()
    {
        return $this->route('product')->urlRewrite?->id;
    }
}
