<?php

use Illuminate\Support\Facades\Route;
use Shopen\Http\Controllers\Frontend\Api\AttributesController;
use Shopen\Http\Controllers\Frontend\Api\CartController as ApiCartController;
use Shopen\Http\Controllers\Frontend\Api\CategoriesController as ApiCategoriesController;
use Shopen\Http\Controllers\Frontend\Api\CheckoutController as ApiCheckoutController;
use Shopen\Http\Controllers\Frontend\Api\ProductReviewsController;
use Shopen\Http\Controllers\Frontend\Api\UsersController as ApiUsersController;
use Shopen\Http\Controllers\Frontend\Cart\CartIndexController;
use Shopen\Http\Controllers\Frontend\Checkout\CheckoutIndexController;
use Shopen\Http\Controllers\Frontend\Checkout\CheckoutLoginController;
use Shopen\Http\Controllers\Frontend\Checkout\CheckoutOrderConfirmationController;
use Shopen\Http\Controllers\Frontend\Checkout\CheckoutOrderController;
use Shopen\Http\Controllers\Frontend\Checkout\CheckoutUpdateController;
use Shopen\Http\Controllers\Frontend\HomeController;
use Shopen\Http\Controllers\Frontend\Product\Review\ProductReviewDeleteController;
use Shopen\Http\Controllers\Frontend\Product\Review\ProductReviewStoreController;
use Shopen\Http\Controllers\Frontend\Product\Review\ProductReviewUpdateController;
use Shopen\Http\Controllers\Frontend\Product\Review\ProductReviewVoteController;
use Shopen\Http\Controllers\Frontend\SearchController;
use Shopen\Http\Controllers\Frontend\ShoppingList\ShoppingListIndexController;
use Shopen\Http\Controllers\Frontend\ShoppingList\ShoppingListItemController;
use Shopen\Http\Controllers\Frontend\ShoppingList\ShoppingListShowController;
use Shopen\Http\Controllers\Frontend\User\Order\UserOrderShowController;
use Shopen\Http\Controllers\Frontend\User\Order\UserOrdersIndexController;
use Shopen\Http\Controllers\Frontend\User\UserAddressesIndexController;
use Shopen\Http\Controllers\Frontend\User\UserSettingsIndexController;
use Shopen\Http\Controllers\Frontend\UserProfileController;
use Shopen\Http\Dispatcher;

if (env('APP_ENV') === 'local') {
    Route::get('/mail/{id?}', function ($id = 1) {
       $order = \Shopen\Models\Order\Order::query()->where('id', $id)->first();
       return new \Shopen\Mail\Order\OrderProcessing($order, 'guwno');
    });
}

Route::middleware(['web'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    //Route::get('/products/{product}', [ProductShowController::class, 'index']);
    //Route::get('/categories/{category}', [CategoryShowController::class, 'index']);
    Route::get('/szukaj', [SearchController::class, 'index'])->name('search.index');
    Route::get('/koszyk', [CartIndexController::class, 'index'])->name('cart.index');
    Route::get('/zamowienie', [CheckoutIndexController::class, 'index'])->name('checkout.index');
    Route::put('/zamowienie/adres-dostawy', [CheckoutUpdateController::class, 'updateShippingAddress'])->name('checkout.update-shipping-address');
    Route::put('/zamowienie/dane-platnosci', [CheckoutUpdateController::class, 'updateBillingAddress'])->name('checkout.update-billing-address');
    Route::put('/zamowienie/wybierz-adres-dostawy', [CheckoutUpdateController::class, 'selectShippingAddress'])->name('checkout.select-shipping-address');
    Route::put('/zamowienie/wybierz-dane-platnosci', [CheckoutUpdateController::class, 'selectBillingAddress'])->name('checkout.select-billing-address');
    Route::put('/zamowienie/metoda-dostawy', [CheckoutUpdateController::class, 'updateShippingMethod'])->name('checkout.update-shipping-method');
    Route::put('/zamowienie/metoda-platnosci', [CheckoutUpdateController::class, 'updatePaymentMethod'])->name('checkout.update-payment-method');
    Route::put('/zamowienie/kod-promocyjny', [CheckoutUpdateController::class, 'updatePromoCode'])->name('checkout.update-promo-code');
    Route::get('/zamowienie/logowanie-lub-rejestracja', [CheckoutLoginController::class, 'index'])->name('checkout.login');

    Route::post('api/cart/add-item', [ApiCartController::class, 'addItem'])->name('api.cart.items.add');
    Route::delete('/api/cart/items/{cartItem}', [ApiCartController::class, 'removeItem'])->name('api.cart.items.delete');
    Route::put('/api/cart/items/{cartItem}', [ApiCartController::class, 'updateItem'])->name('api.cart.items.update');
    Route::get('/api/cart', [ApiCartController::class, 'show']);
    Route::get('/api/cart/items', [ApiCartController::class, 'items']);

    Route::post('/zamowienie', [CheckoutOrderController::class, 'placeOrder'])->name('checkout.place-order');
    Route::get('/potwierdzenie-zamowienia/{order:uuid}', [CheckoutOrderConfirmationController::class, 'index'])->name('checkout.success');

    Route::get('/produkt/{product}/opinie', [ProductReviewsController::class, 'index'])->name('api.products.reviews.index');

    Route::prefix('listy-zakupowe')->name('shopping-lists.')->group(function () {
        Route::get('/', [ShoppingListIndexController::class, 'index'])->name('index');
        Route::get('/{shoppingList}', [ShoppingListShowController::class, 'show'])->name('show');
        Route::post('/', [ShoppingListIndexController::class, 'store'])->name('store');
        Route::put('/{shopping_list}', [ShoppingListIndexController::class, 'update'])->name('update');
        Route::delete('/{shopping_list}', [ShoppingListIndexController::class, 'destroy'])->name('destroy');

        Route::post('/items', [ShoppingListItemController::class, 'store'])->name('items.store');
        Route::delete('/items/{shoppingList}/{product}', [ShoppingListItemController::class, 'destroy'])->name('items.destroy');
    });
});


Route::middleware(['web', 'auth'])->group(function () {

    Route::post('/api/users/billing-addresses/', [ApiUsersController::class, 'storeBillingAddress'])->name('api.users.billing-addresses.store');
    Route::put('/api/users/billing-addresses/{address}', [ApiUsersController::class, 'updateBillingAddress'])->name('api.users.billing-addresses.update');
    Route::post('/api/users/shipping-addresses/', [ApiUsersController::class, 'storeShippingAddress'])->name('api.users.shipping-addresses.store');
    Route::put('/api/users/shipping-addresses/{address}', [ApiUsersController::class, 'updateShippingAddress'])->name('api.users.shipping-addresses.update');

    Route::get('/dane-do-zamowien', [UserAddressesIndexController::class, 'index'])->name('user.addresses.index');
    Route::delete('/adresy/{adres}', [UserAddressesIndexController::class, 'index'])->name('user.addresses.destroy');
    Route::get('/zamowienia', [UserOrdersIndexController::class, 'index'])->name('user.orders.index');
    Route::get('/zamowienia/{order}', [UserOrderShowController::class, 'show'])->name('user.orders.show');
    Route::get('/ustawienia', [UserSettingsIndexController::class, 'index'])->name('user.settings.index');
    Route::post('/ustawienia', [UserSettingsIndexController::class, 'update'])->name('user.settings.update');

    Route::post('/produkt/{product}/opinie', [ProductReviewStoreController::class, 'store'])->name('products.reviews.store');
    Route::put('/produkt/opinie/{review}', [ProductReviewUpdateController::class, 'update'])->name('products.reviews.update');
    Route::delete('/produkt/opinie/{review}', [ProductReviewDeleteController::class, 'delete'])->name('products.reviews.delete');
    Route::post('/produkt/opinie/{review}/ocena', [ProductReviewsController::class, 'vote'])->name('api.products.reviews.vote');
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';

Route::fallback([Dispatcher::class, 'dispatch'])->middleware('web');