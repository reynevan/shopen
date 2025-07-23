<?php

namespace Shopen\Http\Controllers\Frontend\Cart;

use Inertia\Inertia;
use Inertia\Response;

readonly class CartIndexController
{
    public function index(): Response
    {
        return Inertia::render('Frontend/Cart/Index', []);
    }


}