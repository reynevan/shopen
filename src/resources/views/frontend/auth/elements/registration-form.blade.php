<form method="POST" action="{{ route('register') }}">
    @csrf

    <div class="form-field mb-4">
        <label class="text-sm" for="first_name">Imię:</label>
        <input id="first_name" class="block mt-1 w-full" type="text" name="first_name" value="{{ old('first_name') }}"
               required autofocus/>
    </div>
    <div class="form-field mb-4">
        <label class="text-sm" for="last_name">Nazwisko:</label>
        <input id="last_name" class="block mt-1 w-full" type="text" name="last_name" value="{{ old('last_name') }}"
               required autofocus/>
    </div>
    <div class="form-field mb-4">
        <label class="text-sm" for="email">Email:</label>
        <input id="email" class="block mt-1 w-full" type="email" name="email" value="{{ old('email') }}"
               required autofocus/>
    </div>

    <!-- Password -->
    <div class="form-field mb-4">
        <label class="text-sm" for="password">Hasło</label>

        <input id="password" class="block mt-1 w-full"
               type="password"
               name="password"
               required autocomplete="new-password"/>

    </div>
    <div class="form-field mb-4">
        <label class="text-sm" for="password_confirmation">Potwierdź hasło</label>

        <input id="password_confirmation" class="block mt-1 w-full"
               type="password"
               name="password_confirmation"
               required autocomplete="new-password"/>

    </div>

    <div class="flex items-center justify-end mt-4">

        <button class="button-primary">
            {{ __('Załóż konto') }}
        </button>
    </div>
</form>