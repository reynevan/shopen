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
use Shopen\Services\CartService;
use Shopen\Services\MenuService;
use Shopen\Services\ShoppingListService;
use Shopen\Services\TextSlidesService;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{

    public function __construct(
        protected readonly CartService $cartService,
        protected readonly Context $context,
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
        $data = [
            'errors' => fn() => $this->resolveValidationErrors($request),
            'auth' => fn() => [
                'user' => $request->user(),
            ],
            'csrf_token' => fn() => csrf_token(),
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'warning' => fn () => $request->session()->get('warning'),
                'info' => fn () => $request->session()->get('info'),
                /*'validation_status' => fn () => $request->session()->get('validation_status'),
                'validation_message' => fn () => $request->session()->get('validation_message'),
                'validation_summary' => fn () => $request->session()->get('validation_summary'),
                'validation_errors' => fn () => $request->session()->get('validation_errors'),
                'validation_warnings' => fn () => $request->session()->get('validation_warnings'),
                'is_validated' => fn () => $request->session()->get('is_validated'),*/
            ],
            'route' => $request->route()->getName(),
            'turnstile_site_key' => $request->inertia() ? Inertia::lazy(fn () => config('services.turnstile.key')) : fn () => config('services.turnstile.key')
        ];
        if (Route::is('admin.*')) {
            $ziggy = fn () => [
                ...(new Ziggy)->filter('admin.*')->toArray(),
                'location' => $request->url(),
            ];
            $data['ziggy'] = $request->inertia() ? Inertia::lazy($ziggy) : $ziggy;
            $data['referer'] = $request->header('referer');
        } else {
            $ziggy = fn () => [
                ...(new Ziggy)->filter('!admin.*')->toArray(),
                'location' => $request->url(),
            ];
            $data['ziggy'] = $request->inertia() ? Inertia::lazy($ziggy) : $ziggy;
            $data['cart'] = function () use ($request) {
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

            };
            $data['menu'] = $request->inertia()
                ? Inertia::lazy(fn () => app(MenuService::class)->getMenu())
                : fn() => app(MenuService::class)->getMenu();
            $data['breadcrumbs'] = fn() => Breadcrumbs::generate();
            $data['shoppingLists'] = fn () => app(ShoppingListService::class)
                ->getCurrentUserListsQuery()
                ->withCount('products')
                ->orderBy('name')
                ->get();
            $data['gtag_id'] = fn() => config('services.gtm.id');
            $data['textSlides'] = $request->inertia() ?
                Inertia::lazy(fn () => TextSlideResource::collection(app(TextSlidesService::class)->getAll()))
                : fn() => TextSlideResource::collection(app(TextSlidesService::class)->getAll());
        }
        return $data;
    }
}
