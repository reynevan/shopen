@extends('shopen::frontend.layouts.main')

@section('content')
    <div class="flex flex-wrap sm:flex-nowrap max-w-4xl mx-auto">
        <div class="w-full sm:w-1/2 mt-6 px-6 py-4">
            <div class="">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <label for="email">Email:</label>
                        <input id="email" class="block mt-1 w-full" type="email" name="email" value="{{ old('email') }}"
                               required autofocus autocomplete="username"/>

                    </div>

                    <!-- Password -->
                    <div class="mt-4">
                        <label for="password">Hasło</label>

                        <input id="password" class="block mt-1 w-full"
                               type="password"
                               name="password"
                               required autocomplete="current-password"/>

                    </div>

                    <!-- Remember Me -->
                    <div class="block mt-4">
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

        <div class="w-full sm:w-1/2 px-4 py-6">
            <div>
                Nie masz konta?
            </div>
            <div class="flex flex-col items-center">
                <a href="{{ route('checkout.order') }}">Kontynuuj jako gość</a>
                <div class="flex items-center w-full my-2">
                    <div class="w-full h-[1px] bg-neutral-200"></div>
                    <div class="mx-4">lub</div>
                    <div class="w-full h-[1px] bg-neutral-200"></div>
                </div>
                <a href="{{ route('register') }}">Zarejestruj konto</a>
                <div class="w-full">

                    @include('shopen::frontend.auth.elements.registration-form')
                </div>
            </div>
        </div>
    </div>
@endsection