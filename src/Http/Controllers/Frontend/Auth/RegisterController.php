<?php

namespace Shopen\Http\Controllers\Frontend\Auth;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Controller;
use Shopen\Mail\Auth\NewUserMail;
use Shopen\Models\Order\Order;
use Shopen\Models\User;
use Shopen\Services\OAuthProviderService;

class RegisterController extends Controller
{
    public function __construct(protected readonly OAuthProviderService $providerService)
    {}

    public function create(): Response
    {
        $data = [
            'providers' => $this->providerService->getProviders(),
        ];
        if (session('guest_order_id')) {
            $order = Order::find(session('guest_order_id'));
            if ($order && $address = $order->shippingAddress) {
                $data = [
                    'user' => [
                        'first_name' => $address->first_name,
                        'last_name' => $address->last_name,
                        'email' => $address->email,
                    ]
                ];
            }
        }
        return Inertia::render('Frontend/Auth/Register', $data);
    }


    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required',  Rules\Password::defaults()],
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => User::ROLE_USER,
        ]);

        event(new Registered($user));

        Mail::to($user->email)->queue(new NewUserMail($user));

        Auth::login($user);

        if (session('guest_order_id')) {
            $order = Order::find(session('guest_order_id'));
            $order && $order->update(['user_id' => $user->id]);

            session()->forget('guest_order_id');

            return redirect(route('user.orders.index'));
        }

        if ($request->filled('redirectTo')) {
            return redirect()->to($request->input('redirectTo'));
        }
        return redirect('/');
    }
}
