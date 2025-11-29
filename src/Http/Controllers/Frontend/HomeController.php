<?php

namespace Shopen\Http\Controllers\Frontend;

use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Controller;

class HomeController extends Controller
{

    public function index(): Response
    {
        return Inertia::render('Frontend/Home/Index');
    }
}