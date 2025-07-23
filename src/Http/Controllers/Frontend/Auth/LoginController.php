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

class LoginController extends Controller
{

    public function create(): Response
    {
        return Inertia::render('Frontend/Auth/Login', []);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

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
