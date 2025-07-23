<?php

namespace Shopen\Http;

use Illuminate\Contracts\View\View;

abstract class Controller
{
    protected function view($view, $data = [], $mergeData = []):  View
    {
        $viewPath = view()->exists($view) ? $view : 'shopen::' . $view;
        return view($viewPath, $data, $mergeData);
    }
}