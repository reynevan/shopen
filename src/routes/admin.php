<?php


use Shopen\Http\Controllers\Admin\Product\Review\ProductReviewsEditController;
use Shopen\Http\Controllers\Admin\Product\Review\ProductReviewsIndexController;
use Shopen\Http\Controllers\Admin\Category\CategoriesIndexController;
use Illuminate\Support\Facades\Route;
use Shopen\Http\Controllers\Admin\Api\OrdersController as ApiOrdersController;
use Shopen\Http\Controllers\Admin\Api\UploadController;
use Shopen\Http\Controllers\Admin\Banner\BannerCreateController;
use Shopen\Http\Controllers\Admin\Banner\BannerEditController;
use Shopen\Http\Controllers\Admin\Banner\BannersIndexController;
use Shopen\Http\Controllers\Admin\Category\CategoryEditController;
use Shopen\Http\Controllers\Admin\Order\OrderShowController;
use Shopen\Http\Controllers\Admin\Order\OrdersIndexController;
use Shopen\Http\Controllers\Admin\PriceRulesController;
use Shopen\Http\Controllers\Admin\Product\ProductEditController;
use Shopen\Http\Controllers\Admin\Product\ProductsIndexController;
use Shopen\Http\Controllers\Admin\ProductsController;
use Shopen\Http\Controllers\Admin\PromoCode\PromoCodeCreateController;
use Shopen\Http\Controllers\Admin\PromoCode\PromoCodeEditController;
use Shopen\Http\Controllers\Admin\PromoCode\PromoCodesIndexController;
use Shopen\Http\Controllers\Frontend\Api\BannerTrackController;
use Shopen\Http\Middleware\AdminMiddleware;


Route::middleware(['auth', AdminMiddleware::class, 'web'])->group(function () {
    Route::get('/admin/produkty/opinie', [ProductReviewsIndexController::class, 'index'])->name('admin.products.reviews.index');
    Route::put('/admin/produkty/opinie/{review}/accept', [ProductReviewsEditController::class, 'accept'])->name('admin.products.reviews.accept');
    Route::put('/admin/produkty/opinie/{review}/reject', [ProductReviewsEditController::class, 'reject'])->name('admin.products.reviews.reject');
    Route::delete('/admin/produkty/opinie/{review}', [ProductReviewsEditController::class, 'delete'])->name('admin.products.reviews.delete');

    Route::get('/admin/produkty', [ProductsIndexController::class, 'index'])->name('admin.products.index');
    Route::get('/admin/produkty/nowy', [ProductsController::class, 'create'])->name('admin.products.create');
    Route::get('/admin/produkty/{product}', [ProductEditController::class, 'edit'])->name('admin.products.edit');
    Route::put('/admin/produkty/{product}', [ProductEditController::class, 'update'])->name('admin.products.update');


    Route::get('/admin/produkty/reguly-cenowe/nowa', [PriceRulesController::class, 'create'])->name('admin.products.price-rules.create');
    //Route::post('/admin/api/products/price-rules', [ApiPriceRulesController::class, 'store'])->name('admin.api.products.price-rules.store');

    Route::post('/admin/api/upload-image', [UploadController::class, 'uploadImage'])->name('admin.api.products.upload-image');

    Route::get('/admin/kategorie', [CategoriesIndexController::class, 'index'])->name('admin.categories.index');
    Route::get('/admin/kategorie/{category}', [CategoryEditController::class, 'edit'])->name('admin.categories.edit');
    Route::put('/admin/kategorie/{category}', [CategoryEditController::class, 'update'])->name('admin.categories.update');

    Route::get('/admin/zamowienia', [OrdersIndexController::class, 'index'])->name('admin.orders.index');
    Route::get('/admin/zamowienia/{order}', [OrderShowController::class, 'show'])->name('admin.orders.show');
    Route::post('/admin/zamowienia/{order}/status', [OrderShowController::class, 'updateStatus'])->name('admin.orders.updateStatus');
    Route::post('/admin/zamowienia/{order}/wysylka', [OrderShowController::class, 'updateShipping'])->name('admin.orders.shipping');
    Route::get('/admin/api/orders', [ApiOrdersController::class, 'index']);

    Route::get('/admin/kody-promocyjne', [PromoCodesIndexController::class, 'index'])->name('admin.promo-codes.index');
    Route::get('/admin/kody-promocyjne/nowy', [PromoCodeCreateController::class, 'create'])->name('admin.promo-codes.create');
    Route::post('/admin/kody-promocyjne', [PromoCodeCreateController::class, 'store'])->name('admin.promo-codes.store');
    Route::get('/admin/kody-promocyjne/{promoCode}/edycja', [PromoCodeEditController::class, 'edit'])->name('admin.promo-codes.edit');
    Route::put('/admin/kody-promocyjne/{promoCode}', [PromoCodeEditController::class, 'update'])->name('admin.promo-codes.update');

    Route::get('/admin/bannery', [BannersIndexController::class, 'index'])->name('admin.banners.index');
    Route::get('/admin/bannery/nowy', [BannerCreateController::class, 'create'])->name('admin.banners.create');
    Route::post('/admin/bannery', [BannerCreateController::class, 'store'])->name('admin.banners.store');
    Route::get('/admin/bannery/{banner}/edycja', [BannerEditController::class, 'edit'])->name('admin.banners.edit');
    Route::put('/admin/bannery/{banner}', [BannerEditController::class, 'update'])->name('admin.banners.update');
    Route::post('/api/banners/track/{banner}', BannerTrackController::class)->name('banners.track');

    Route::post('/admin/api/upload-image', [UploadController::class, 'uploadImage']);


});
