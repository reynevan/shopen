<?php

namespace Shopen\Http\Controllers\Admin\Settings;

use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Core\Payment\PaymentMethodManager;
use Shopen\Http\Resources\Admin\TextSlide\TextSlideResource;
use Shopen\Models\TextSlide\TextSlide;
use Shopen\Services\ConfigService;
use Shopen\Services\TextSlidesService;

class PaymentMethodsController
{
    public function __construct(
        private PaymentMethodManager $paymentMethodManager,
        private ConfigService $configService
    )
    {}

    public function index(): Response
    {
        $paymentMethods = $this->paymentMethodManager->getAllPaymentMethods();
        $methods = [];
        foreach ($paymentMethods as $method) {
            $methods[] = $method->toArray();
        }
        return Inertia::render('Admin/Settings/PaymentMethods', [
            'methods' => $methods
        ]);
    }

    public function update(): RedirectResponse
    {
        $configKeys = ['active', 'title', 'description'];
        foreach (request('methods') as $method) {
            foreach ($configKeys as $configKey) {
                if (isset($method[$configKey])) {
                    $this->configService->save('payment/' . $method['key'] . '/' . $configKey, $method[$configKey]);
                }
            }
            if (isset($method['additional_fields'])) {
                foreach ($method['additional_fields'] as $field) {
                    $this->configService->save('payment/' . $method['key'] . '/' . $field['key'], $field['value']);
                }
            }
        }
        return back()->with('success', 'Zmiany zostały zapisane');
    }
}