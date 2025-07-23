<?php

namespace Shopen\Http\Controllers\Frontend\Checkout;

use Inertia\Inertia;
use Inertia\Response;

readonly class CheckoutLoginController
{

    public function index(): Response
    {
        return Inertia::render('Frontend/Checkout/Login');
    }
}