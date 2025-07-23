<header class="header py-6 sticky top-0 sm:relative z-10">
    <div class="flex justify-between items-center">
        <div class="flex items-center">
            <div class="visible sm:hidden mr-4">
                <menu-button></menu-button>
            </div>
            <div class="font-2xl flex justify-center">
                LOGO
            </div>
        </div>
        <div class="flex justify-between items-center mx-6">
            @auth
                <div class="group">
                    <div class="">
                        <a href="{{ route('user.orders.index') }}">Moje konto</a>
                    </div>
                    <div class="absolute invisible group-hover:visible">
                        <div>
                            <a href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Wyloguj</a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                  style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            @endauth
            @guest
                <div>
                    <a href="{{ route('login') }}">Logowanie</a> | <a href="{{ route('register') }}">Rejestracja</a>
                </div>
            @endguest
            <div class="ml-4">
                @block('cart.minicart')
            </div>
        </div>
    </div>
</header>
<div class="nav-panel z-30 sm:z-auto transition-[left] duration-500 top-0 bottom-0 fixed sm:relative" id="nav-panel">
    @block('header.categories')
</div>