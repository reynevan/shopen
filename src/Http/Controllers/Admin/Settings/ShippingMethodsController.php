<?php

namespace Shopen\Http\Controllers\Admin\Settings;

use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Core\Shipping\ShippingMethodManager;
use Shopen\Services\ConfigService;

class ShippingMethodsController
{
    public function __construct(
        private readonly ShippingMethodManager $shippingMethodManager,
        private readonly ConfigService $configService
    )
    {}

    public function index(): Response
    {
        $shippingMethods = $this->shippingMethodManager->getAllShippingMethods();
        $methods = [];
        foreach ($shippingMethods as $method) {
            $methods[] = $method->toArray();
        }
        return Inertia::render('Admin/Settings/ShippingMethods', [
            'methods' => $methods
        ]);
    }

    public function update(): RedirectResponse
    {
        $configKeys = ['active', 'title', 'price', 'description', 'free_shipping_available', 'free_shipping_threshold'];
        foreach (request('methods') as $method) {
            foreach ($configKeys as $configKey) {
                if (isset($method[$configKey])) {
                    $this->configService->save('shipping/' . $method['key'] . '/' . $configKey, $method[$configKey]);
                }
            }
        }
        return back()->with('success', 'Zmiany zostały zapisane');
    }
}