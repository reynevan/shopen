<?php

namespace Shopen\Http\Controllers\Frontend\Auth;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Controller;
use Shopen\Http\Requests\Admin\Auth\LoginRequest;
use Shopen\Models\Order\Order;
use Shopen\Services\OAuthProviderService;

class LoginController extends Controller
{
    public function __construct(protected readonly OAuthProviderService $providerService)
    {}

    public function create(): Response
    {
        return Inertia::render('Frontend/Auth/Login', [
            'providers' => $this->providerService->getProviders(),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        if (session('guest_order_id')) {
            $order = Order::find(session('guest_order_id'));
            $order->update(['user_id' => Auth::id()]);

            session()->forget('guest_order_id');

            return redirect(route('user.orders.index'));
        }

        if ($request->filled('redirectTo')) {
            return redirect()->to($request->input('redirectTo'));
        }

        $redirect = Auth::user()->isAdmin() ? url(route('admin.products.index')) : url('/');
        return redirect()->intended($redirect);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect(url('/'));
    }
}
