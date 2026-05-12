<?php

namespace Shopen\Http\Controllers\Frontend\Static;

use Inertia\Inertia;
use Inertia\Response;

class AboutController
{
    public function index(): Response
    {
        return Inertia::render('Frontend/Static/About');
    }
}
