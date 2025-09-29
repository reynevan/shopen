<?php

namespace Shopen\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Shopen\Core\Context;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function __construct(private Context $context)
    {}

    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        $user = Auth::guard('admin')->user();
        if (!$user->isAdmin()) {
            Auth::guard('admin')->logout();
            return redirect()->route('admin.login')->with('error', 'Brak uprawnień administratora');
        }
        $this->context->setIsAdmin(true);

        return $next($request);
    }
}
