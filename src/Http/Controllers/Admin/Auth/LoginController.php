<?php

namespace Shopen\Http\Controllers\Admin\Auth;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Controller;
use Shopen\Http\Requests\Admin\Auth\LoginRequest;
use Shopen\Services\OAuthProviderService;

class LoginController extends Controller
{
    public function __construct(protected readonly OAuthProviderService $providerService)
    {}

    public function create()
    {
        if (Auth::check()) {
            return redirect(url('/admin'));
        }
        return Inertia::render('Admin/Auth/Login', [
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

        return redirect()->intended(url('/admin'));
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
