<?php

namespace Shopen\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Number;
use Inertia\Middleware;
use Shopen\Core\Context;
use Shopen\Facades\Breadcrumbs;
use Shopen\Http\Resources\Cart\CartItemResource;
use Shopen\Services\BreadcrumbsService;
use Shopen\Services\CartService;
use Shopen\Services\MenuService;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{

    public function __construct(
        protected readonly CartService $cartService,
        protected readonly Context $context,
    )
    {
        $this->rootView = $this->context->isAdmin() ? 'admin' : 'app';
    }

    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $data = [
            'errors' => fn() => $this->resolveValidationErrors($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'ziggy' => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'csrf_token' => csrf_token(),
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'warning' => fn () => $request->session()->get('warning'),
                'info' => fn () => $request->session()->get('info'),
            ],
        ];
        if ($this->context->isAdmin()) {

        } else {
            $data['cart'] = function () use ($request) {
                $itemsCount = 0;
                $subtotal = 0;
                $items = [];
                if ($this->cartService->hasCart()) {
                    $cart = $this->cartService->getCart();
                    $items = CartItemResource::collection($cart->items)->toArray($request);
                    $itemsCount = $cart->itemCount();
                    $subtotal = $cart->totalPrice();
                }

                return [
                    'itemsCount' => $itemsCount,
                    'items' => $items,
                    'subtotal' => Number::currency($subtotal)
                ];

            };
            $data['menu'] = [
                'categories' => fn() => app(MenuService::class)->getCategories(),
            ];
            $data['breadcrumbs'] = fn() => Breadcrumbs::generate();
        }
        return $data;
    }
}
