<?php

namespace Shopen\Core\Payment;

use Illuminate\Support\Facades\File;
use Shopen\Core\Payment\Methods\PaymentMethodInterface;


class PaymentMethodManager
{
    protected array $methods = [];

    protected function registerPaymentMethodsFromNamespace($path, $namespace): void
    {
        if (!file_exists($path)) {
            return;
        }
        $methodFiles = File::files($path);

        foreach ($methodFiles as $file) {
            $class = $namespace . $file->getFilenameWithoutExtension();

            if (!class_exists($class)) {
                continue;
            }
            $reflection = new \ReflectionClass($class);
            if ($reflection->isAbstract()) {
                continue;
            }
            $instance = app($class);

            if ($instance instanceof PaymentMethodInterface) {
                $this->register($instance);
            }
        }
    }

    protected function registerMethods(): void
    {
        if (count($this->methods)) {
            return;
        }
        $this->registerPaymentMethodsFromNamespace(app_path('Payment/Methods'), 'App\\Payment\\Methods\\');
        $this->registerPaymentMethodsFromNamespace(__DIR__ . '/Methods', 'Shopen\\Core\\Payment\\Methods\\');
    }

    public function register(PaymentMethodInterface $method): void
    {
        if (isset($this->methods[$method->getKey()])) {
            return;
        }
        $this->methods[$method->getKey()] = $method;
    }

    public function getPaymentMethods(): array
    {
        $this->registerMethods();
        return array_filter(array_values($this->methods), fn ($method) => $method->isAvailable());
    }

    public function get(string $key): ?PaymentMethodInterface
    {
        $this->registerMethods();
        return $this->methods[$key] ?? null;
    }
}