<?php

namespace Shopen\Core\Shipping;

use Illuminate\Support\Facades\File;
use Shopen\Core\Shipping\Methods\ShippingMethodInterface;
use Shopen\Services\CartService;

class ShippingMethodManager
{
    public function __construct(
        protected CartService $cartService,
    )
    {}

    protected array $methods = [];

    protected function registerShippingMethodsFromNamespace($path, $namespace): void
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

            if ($instance instanceof ShippingMethodInterface && config("shipping.{$instance->getKey()}.active")) {
                $this->register($instance);
            }
        }
    }

    protected function registerMethods(): void
    {
        if (count($this->methods)) {
            return;
        }
        $this->registerShippingMethodsFromNamespace(app_path('Shipping/Methods'), 'App\\Shipping\\Methods\\');
        $this->registerShippingMethodsFromNamespace(__DIR__ . '/Methods', 'Shopen\\Core\\Shipping\\Methods\\');
    }

    public function register(ShippingMethodInterface $method): void
    {
        $this->methods[$method->getKey()] = $method;
    }

    public function getShippingMethods(): array
    {
        $this->registerMethods();
        if ($this->cartService->getCart()->hasOnlyVirtualItems()) {
            $methods = [];
            foreach ($this->methods as $method) {
                if ($method->isVirtual()) {
                    $methods[] = $method;
                }
            }
            return $methods;;
        } else {
            return array_values($this->methods);
        }
    }

    public function get(string $key): ?ShippingMethodInterface
    {
        $this->registerMethods();
        return $this->methods[$key] ?? null;
    }
}