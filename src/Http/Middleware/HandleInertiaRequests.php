<?php

namespace Shopen\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Number;
use Inertia\Inertia;
use Inertia\Middleware;
use Shopen\Core\Context;
use Shopen\Facades\Breadcrumbs;
use Shopen\Http\Resources\Admin\TextSlide\TextSlideResource;
use Shopen\Http\Resources\Cart\CartItemResource;
use Shopen\Http\Resources\User\UserResource;
use Shopen\Models\Cart\Cart;
use Shopen\Services\CartService;
use Shopen\Services\MenuService;
use Shopen\Services\ShoppingListService;
use Shopen\Services\TextSlidesService;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{

    public function __construct(
        protected readonly CartService $cartService,
        protected readonly Context     $context,
    )
    {
        $this->rootView = Route::is('admin.*') ? 'admin' : 'app';
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
        $user = $request->user();
        $data = [
            'errors' => fn() => $this->resolveValidationErrors($request),
            'auth' => fn() => !!$user,
            'csrf_token' => fn() => csrf_token(),
            'route' => $request->route()->getName(),
            ...$this->getFlashData($request),
        ];
        if (Route::is('admin.*')) {
            $ziggy = fn() => [
                ...(new Ziggy)->filter('admin.*')->toArray(),
                'location' => $request->url(),
            ];
            $data['ziggy'] = $request->inertia() ? Inertia::lazy($ziggy) : $ziggy;
            $data['referer'] = $request->header('referer');
        } else {
            $ziggy = fn() => [
                ...(new Ziggy)->filter('!admin.*')->toArray(),
                'location' => $request->url(),
            ];
            $data['ziggy'] = $request->inertia() ? Inertia::lazy($ziggy) : $ziggy;


            $data['cart'] = Inertia::lazy(fn() => $this->getCart($request));
            $data['menu'] = $request->inertia()
                ? Inertia::lazy(fn() => app(MenuService::class)->getMenu())
                : fn() => app(MenuService::class)->getMenu();
            $data['breadcrumbs'] = fn() => Breadcrumbs::generate();
            $data['text_slides'] = $request->inertia() ?
                Inertia::lazy(fn() => TextSlideResource::collection(app(TextSlidesService::class)->getAll()))
                : fn() => TextSlideResource::collection(app(TextSlidesService::class)->getAll());
            $data['site_name'] = fn() => config('app.name');

            $data['config'] = fn() => [
                'max_cart_products' => config('shopen.cart.max_item_qty', 10)
            ];
        }
        return $data;
    }

    protected function getFlashData(Request $request): array
    {
        $data = [];
        $types = ['success', 'error', 'warning', 'info'];
        foreach ($types as $type) {
            $value = $request->session()->get($type);
            if ($value) {
                $data[$type] = $value;
            }
        }
        return count($data) ? ['flash' => $data] : [];
    }

    protected function getCart(Request $request): array
    {
        $subtotal = 0;
        $items = [];
        if ($this->cartService->hasCart()) {
            $cart = $this->cartService->getCart();
            $items = CartItemResource::collection($cart->items)->toArray($request);
            $subtotal = $cart->totalPrice();
        }

        return [
            'items' => $items,
            'subtotal' => Number::currency($subtotal)
        ];
    }
}
