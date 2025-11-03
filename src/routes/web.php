<?php

use Illuminate\Support\Facades\Route;
use Shopen\Http\Controllers\Frontend\Api\AttributesController;
use Shopen\Http\Controllers\Frontend\Api\CartController as ApiCartController;
use Shopen\Http\Controllers\Frontend\Api\CategoriesController as ApiCategoriesController;
use Shopen\Http\Controllers\Frontend\Api\CheckoutController as ApiCheckoutController;
use Shopen\Http\Controllers\Frontend\Api\ProductReviewsController;
use Shopen\Http\Controllers\Frontend\Api\UsersController as ApiUsersController;
use Shopen\Http\Controllers\Frontend\Brand\BrandShowController;
use Shopen\Http\Controllers\Frontend\Cart\CartIndexController;
use Shopen\Http\Controllers\Frontend\Checkout\CheckoutIndexController;
use Shopen\Http\Controllers\Frontend\Checkout\CheckoutLoginController;
use Shopen\Http\Controllers\Frontend\Checkout\CheckoutOrderConfirmationController;
use Shopen\Http\Controllers\Frontend\Checkout\CheckoutOrderController;
use Shopen\Http\Controllers\Frontend\Checkout\CheckoutUpdateController;
use Shopen\Http\Controllers\Frontend\HomeController;
use Shopen\Http\Controllers\Frontend\Newsletter\NewsletterController;
use Shopen\Http\Controllers\Frontend\Payment\PaymentPayController;
use Shopen\Http\Controllers\Frontend\Payment\Payu\PayuNotifyController;
use Shopen\Http\Controllers\Frontend\Product\Review\ProductReviewDeleteController;
use Shopen\Http\Controllers\Frontend\Product\Review\ProductReviewStoreController;
use Shopen\Http\Controllers\Frontend\Product\Review\ProductReviewUpdateController;
use Shopen\Http\Controllers\Frontend\Product\Review\ProductReviewVoteController;
use Shopen\Http\Controllers\Frontend\SearchController;
use Shopen\Http\Controllers\Frontend\ShoppingList\ShoppingListIndexController;
use Shopen\Http\Controllers\Frontend\ShoppingList\ShoppingListItemController;
use Shopen\Http\Controllers\Frontend\ShoppingList\ShoppingListShowController;
use Shopen\Http\Controllers\Frontend\Static\ContactController;
use Shopen\Http\Controllers\Frontend\User\Order\UserOrderCancelController;
use Shopen\Http\Controllers\Frontend\User\Order\UserOrderPayController;
use Shopen\Http\Controllers\Frontend\User\Order\UserOrderShowController;
use Shopen\Http\Controllers\Frontend\User\Order\UserOrdersIndexController;
use Shopen\Http\Controllers\Frontend\User\UserAddressesIndexController;
use Shopen\Http\Controllers\Frontend\User\UserSettingsIndexController;
use Shopen\Http\Controllers\Frontend\UserProfileController;
use Shopen\Http\Dispatcher;
use \Shopen\Http\Controllers\Frontend\Api\SearchController as ApiSearchController;

if (env('APP_ENV') === 'local') {
    Route::get('/mail/{id?}', function ($id = 1) {
       $order = \Shopen\Models\Order\Order::query()->where('id', $id)->first();
       return new \Shopen\Mail\Order\OrderVouchers($order, [['name' => 'Bon 100 zł', 'codes' => ['ABC100', 'ASD123123']], ['name' => 'Bon 200 zł', 'codes' => ['ADG123', 'SDFAFF234234']]]);
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
    Route::post('/zamowienie/{order:uuid}/zaplac', [UserOrderPayController::class, 'pay'])->name('order.pay');

    Route::post('api/cart/add-item', [ApiCartController::class, 'addItem'])->name('api.cart.items.add');
    Route::delete('/api/cart/items/{cartItem}', [ApiCartController::class, 'removeItem'])->name('api.cart.items.delete');
    Route::put('/api/cart/items/{cartItem}', [ApiCartController::class, 'updateItem'])->name('api.cart.items.update');
    Route::get('/api/cart', [ApiCartController::class, 'show']);
    Route::get('/api/cart/items', [ApiCartController::class, 'items']);

    Route::post('/zamowienie', [CheckoutOrderController::class, 'placeOrder'])->name('checkout.place-order');
    Route::get('/potwierdzenie-zamowienia/{order:uuid}', [CheckoutOrderConfirmationController::class, 'index'])->name('checkout.success');

    Route::get('/produkt/{product}/opinie', [ProductReviewsController::class, 'index'])->name('api.products.reviews.index');

    Route::get('/marka/{brand}', [BrandShowController::class, 'show'])->name('brands.show');

    Route::prefix('listy-zakupowe')->name('user.shopping-lists.')->group(function () {
        Route::get('/', [ShoppingListIndexController::class, 'index'])->name('index');
        Route::get('/{shoppingList}', [ShoppingListShowController::class, 'show'])->name('show');
        Route::post('/', [ShoppingListIndexController::class, 'store'])->name('store');
        Route::put('/{shopping_list}', [ShoppingListIndexController::class, 'update'])->name('update');
        Route::delete('/{shopping_list}', [ShoppingListIndexController::class, 'destroy'])->name('destroy');

        Route::post('/items', [ShoppingListItemController::class, 'store'])->name('items.store');
        Route::put('/', [ShoppingListItemController::class, 'update'])->name('items.update');
        Route::delete('/items/{shoppingList}/{product}', [ShoppingListItemController::class, 'destroy'])->name('items.destroy');
    });
    Route::get('/zamowienia/{order:uuid}', [UserOrderShowController::class, 'show'])->name('user.orders.show');
    Route::put('/zamowienia/{order:uuid}/anuluj', [UserOrderCancelController::class, 'cancel'])->name('user.orders.cancel');

    Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
    Route::post('/newsletter/unsubscribe', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');
    Route::get('/newsletter/potwierdzenie-subskrypcji', [NewsletterController::class, 'confirmed'])->name('newsletter.confirmed');

    Route::get('/kontakt', [ContactController::class, 'index'])->name('contact.index');
    Route::post('/kontakt', [ContactController::class, 'submit'])->name('contact.submit');

    Route::post('/payu/notify', [PayuNotifyController::class, 'index'])->name('payu.notify');
});


Route::middleware(['web', 'auth'])->group(function () {

    Route::post('/api/users/billing-addresses/', [ApiUsersController::class, 'storeBillingAddress'])->name('api.users.billing-addresses.store');
    Route::put('/api/users/billing-addresses/{address}', [ApiUsersController::class, 'updateBillingAddress'])->name('api.users.billing-addresses.update');
    Route::post('/api/users/shipping-addresses/', [ApiUsersController::class, 'storeShippingAddress'])->name('api.users.shipping-addresses.store');
    Route::put('/api/users/shipping-addresses/{address}', [ApiUsersController::class, 'updateShippingAddress'])->name('api.users.shipping-addresses.update');
    Route::put('/api/users/addresses/{address}/default', [ApiUsersController::class, 'setAddressDefault'])->name('api.users.addresses.update-default');
    Route::delete('/api/users/addresses/{address}', [ApiUsersController::class, 'removeAddress'])->name('api.users.addresses.destroy');

    Route::get('/dane-do-zamowien', [UserAddressesIndexController::class, 'index'])->name('user.addresses.index');
    Route::get('/zamowienia', [UserOrdersIndexController::class, 'index'])->name('user.orders.index');
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