<?php

namespace Shopen\Http\Controllers\Frontend\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class OAuthController
{
    public function callback($provider)
    {
        try {
            $oauthUser = Socialite::driver($provider)->user();

            $user = User::updateOrCreate([
                'email' => $oauthUser->email
            ], [
                'first_name' => $oauthUser->user['given_name'] ?? $oauthUser->name,
                'last_name' => $oauthUser->user['family_name'] ?? $oauthUser->name,
                $provider . '_id' => $oauthUser->id,
                $provider . '_token' => $oauthUser->token,
                $provider . '_refresh_token' => $oauthUser->refreshToken,
                'password' => Hash::make(Str::random()),
            ]);

            Auth::login($user);
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Nie udało się zalogować.');
        }
        return redirect('/');
    }
}