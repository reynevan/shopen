<?php


use Shopen\Http\Controllers\Admin\Attribute\AttributeCreateController;
use Shopen\Http\Controllers\Admin\Attribute\AttributeEditController;
use Shopen\Http\Controllers\Admin\Attribute\AttributeIndexController;
use Shopen\Http\Controllers\Admin\Brand\BrandCreateController;
use Shopen\Http\Controllers\Admin\Brand\BrandIndexController;
use Shopen\Http\Controllers\Admin\Dashboard\DashboardIndexController;
use Shopen\Http\Controllers\Admin\Product\ProductCreateController;
use Shopen\Http\Controllers\Admin\Product\Review\ProductReviewsEditController;
use Shopen\Http\Controllers\Admin\Product\Review\ProductReviewsIndexController;
use Shopen\Http\Controllers\Admin\Category\CategoriesIndexController;
use Illuminate\Support\Facades\Route;
use Shopen\Http\Controllers\Admin\Api\OrdersController as ApiOrdersController;
use Shopen\Http\Controllers\Admin\Api\UploadController;
use Shopen\Http\Controllers\Admin\Api\ProductsController as ApiProductsController;
use Shopen\Http\Controllers\Admin\Banner\BannerCreateController;
use Shopen\Http\Controllers\Admin\Banner\BannerEditController;
use Shopen\Http\Controllers\Admin\Banner\BannersIndexController;
use Shopen\Http\Controllers\Admin\Category\CategoryEditController;
use Shopen\Http\Controllers\Admin\Order\OrderShowController;
use Shopen\Http\Controllers\Admin\Order\OrdersIndexController;
use Shopen\Http\Controllers\Admin\Product\ProductEditController;
use Shopen\Http\Controllers\Admin\Product\ProductsIndexController;
use Shopen\Http\Controllers\Admin\PromoCode\PromoCodeCreateController;
use Shopen\Http\Controllers\Admin\PromoCode\PromoCodeEditController;
use Shopen\Http\Controllers\Admin\PromoCode\PromoCodesIndexController;
use Shopen\Http\Controllers\Admin\User\UserEditController;
use Shopen\Http\Controllers\Admin\User\UserIndexController;
use Shopen\Http\Controllers\Frontend\Api\BannerTrackController;
use Shopen\Http\Middleware\AdminMiddleware;


Route::middleware(['auth', AdminMiddleware::class, 'web'])->prefix('/admin')->group(function () {
    Route::get('', [DashboardIndexController::class, 'index'])->name('admin.dashboard');
    Route::get('/produkty/opinie', [ProductReviewsIndexController::class, 'index'])->name('admin.products.reviews.index');
    Route::put('/produkty/opinie/{review}/accept', [ProductReviewsEditController::class, 'accept'])->name('admin.products.reviews.accept');
    Route::put('/produkty/opinie/{review}/reject', [ProductReviewsEditController::class, 'reject'])->name('admin.products.reviews.reject');
    Route::delete('/produkty/opinie/{review}', [ProductReviewsEditController::class, 'delete'])->name('admin.products.reviews.delete');

    Route::get('/produkty', [ProductsIndexController::class, 'index'])->name('admin.products.index');
    Route::get('/api/produkty', [ApiProductsController::class, 'index'])->name('admin.api.products.index');
    Route::get('/produkty/nowy', [ProductCreateController::class, 'create'])->name('admin.products.create');
    Route::get('/produkty/{product}', [ProductEditController::class, 'edit'])->name('admin.products.edit');
    Route::put('/produkty/{product}', [ProductEditController::class, 'update'])->name('admin.products.update');



    //Route::get('/produkty/reguly-cenowe/nowa', [PriceRulesController::class, 'create'])->name('admin.products.price-rules.create');
    //Route::post('/api/products/price-rules', [ApiPriceRulesController::class, 'store'])->name('admin.api.products.price-rules.store');

    Route::post('/api/upload-image', [UploadController::class, 'uploadImage'])->name('admin.api.products.upload-image');

    Route::get('/kategorie', [CategoriesIndexController::class, 'index'])->name('admin.categories.index');
    Route::get('/kategorie/{category}', [CategoryEditController::class, 'edit'])->name('admin.categories.edit');
    Route::put('/kategorie/{category}', [CategoryEditController::class, 'update'])->name('admin.categories.update');

    Route::get('/zamowienia', [OrdersIndexController::class, 'index'])->name('admin.orders.index');
    Route::get('/zamowienia/{order}', [OrderShowController::class, 'show'])->name('admin.orders.show');
    Route::post('/zamowienia/{order}/status', [OrderShowController::class, 'updateStatus'])->name('admin.orders.updateStatus');
    Route::post('/zamowienia/{order}/wysylka', [OrderShowController::class, 'updateShipping'])->name('admin.orders.shipping');
    Route::get('/api/orders', [ApiOrdersController::class, 'index']);

    Route::get('/kody-promocyjne', [PromoCodesIndexController::class, 'index'])->name('admin.promo-codes.index');
    Route::get('/kody-promocyjne/nowy', [PromoCodeCreateController::class, 'create'])->name('admin.promo-codes.create');
    Route::post('/kody-promocyjne', [PromoCodeCreateController::class, 'store'])->name('admin.promo-codes.store');
    Route::get('/kody-promocyjne/{promoCode}/edycja', [PromoCodeEditController::class, 'edit'])->name('admin.promo-codes.edit');
    Route::put('/kody-promocyjne/{promoCode}', [PromoCodeEditController::class, 'update'])->name('admin.promo-codes.update');

    Route::get('/bannery', [BannersIndexController::class, 'index'])->name('admin.banners.index');
    Route::get('/bannery/nowy', [BannerCreateController::class, 'create'])->name('admin.banners.create');
    Route::post('/bannery', [BannerCreateController::class, 'store'])->name('admin.banners.store');
    Route::get('/bannery/{banner}/edycja', [BannerEditController::class, 'edit'])->name('admin.banners.edit');
    Route::put('/bannery/{banner}', [BannerEditController::class, 'update'])->name('admin.banners.update');
    Route::post('/api/banners/track/{banner}', BannerTrackController::class)->name('banners.track');

    Route::get('/atrybuty', [AttributeIndexController::class, 'index'])->name('admin.attributes.index');
    Route::get('/atrybuty/nowy', [AttributeCreateController::class, 'create'])->name('admin.attributes.create');
    Route::post('/atrybuty', [AttributeCreateController::class, 'store'])->name('admin.attributes.store');
    Route::get('/atrybuty/{attribute}', [AttributeEditController::class, 'edit'])->name('admin.attributes.edit');
    Route::put('/atrybuty/{attribute}', [AttributeEditController::class, 'update'])->name('admin.attributes.update');

    Route::get('/uzytkownicy', [UserIndexController::class, 'index'])->name('admin.users.index');
    Route::get('/uzytkownicy/{user}', [UserEditController::class, 'edit'])->name('admin.users.edit');

    Route::get('/marki', [BrandIndexController::class, 'index'])->name('admin.brands.index');
    Route::post('/marki', [BrandCreateController::class, 'store'])->name('admin.brands.store');
    Route::get('/marki/nowa', [BrandCreateController::class, 'create'])->name('admin.brands.create');
    Route::get('/marki/{brand}', [BrandIndexController::class, 'index'])->name('admin.brands.edit');
    Route::put('/marki/{brand}', [BrandIndexController::class, 'index'])->name('admin.brands.update');

    Route::post('/api/upload-image', [UploadController::class, 'uploadImage']);


});
