@extends('shopen::frontend.layouts.main')

@section('content')
    <div class="flex">
        <div class="w-1/2 mt-6 px-6 py-4">
            <div class="w-md">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email Address -->
                    <div class="form-field mb-4">
                        <label for="email" class="text-sm">Email:</label>
                        <input id="email"  type="email" name="email" value="{{ old('email') }}"
                               required autofocus autocomplete="username"/>

                    </div>

                    <!-- Password -->
                    <div class="form-field mb-4">
                        <label for="password" class="text-sm">Hasło</label>

                        <input id="password"
                               type="password"
                               name="password"
                               required autocomplete="current-password"/>

                    </div>

                    <!-- Remember Me -->
                    <div class="block form-field">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox"
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                   name="remember">
                            <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                        </label>
                    </div>

                    <div class="mt-4">
                        @if (Route::has('password.request'))
                            <a class="mb-2 underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                               href="{{ route('password.request') }}">
                                {{ __('Nie pamiętasz hasła?') }}
                            </a>
                        @endif

                        <button class="button-primary">
                            {{ __('Zaloguj się') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="w-1/2">
            <a href="{{ route('register') }}">Zarejestruj konto</a>
        </div>
    </div>
@endsection