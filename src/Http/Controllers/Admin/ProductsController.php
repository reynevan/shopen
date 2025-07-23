<?php

namespace Shopen\Http\Controllers\Admin;

use Illuminate\Contracts\View\View;
use Shopen\Http\Controller;
use Shopen\Models\Product\Product;

class ProductsController extends Controller
{
    public function index(): View
    {
        $products = Product::query()->paginate();

        return $this->view('admin.product.index', compact('products'));
    }

    public function edit(Product $product): View
    {
        return $this->view('admin.product.edit', compact('product'));
    }

    public function create()
    {
        return $this->view('admin.product.create');
    }
}
