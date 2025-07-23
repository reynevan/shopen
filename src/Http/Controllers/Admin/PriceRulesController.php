<?php

namespace Shopen\Http\Controllers\Admin;

use Illuminate\Contracts\View\View;
use Shopen\Core\Context;
use Shopen\Events\Product\Price\ProductPriceRuleUpdated;
use Shopen\Http\Controller;
use Shopen\Models\Product\Price\ProductPriceRule;

class PriceRulesController extends Controller
{
    public function create(): View
    {
        return $this->view('admin.product.price-rules.create');
    }
}
