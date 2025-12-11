<?php


use Shopen\Http\Controllers\Admin\Attribute\AttributeCreateController;
use Shopen\Http\Controllers\Admin\Attribute\AttributeEditController;
use Shopen\Http\Controllers\Admin\Attribute\AttributeIndexController;
use Shopen\Http\Controllers\Admin\Auth\LoginController;
use Shopen\Http\Controllers\Admin\Brand\BrandCreateController;
use Shopen\Http\Controllers\Admin\Brand\BrandEditController;
use Shopen\Http\Controllers\Admin\Brand\BrandIndexController;
use Shopen\Http\Controllers\Admin\Cache\CacheController;
use Shopen\Http\Controllers\Admin\Category\CategoryCreateController;
use Shopen\Http\Controllers\Admin\ContactMessages\ContactMessageEditController;
use Shopen\Http\Controllers\Admin\ContactMessages\ContactMessageIndexController;
use Shopen\Http\Controllers\Admin\ContactMessages\ContactMessageShowController;
use Shopen\Http\Controllers\Admin\Dashboard\DashboardIndexController;
use Shopen\Http\Controllers\Admin\Product\ProductCreateController;
use Shopen\Http\Controllers\Admin\Product\ProductsExportController;
use Shopen\Http\Controllers\Admin\Product\ProductsImportController;
use Shopen\Http\Controllers\Admin\Product\ProductsImportValidationController;
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
use Shopen\Http\Controllers\Admin\Settings\InstagramSettingsController;
use Shopen\Http\Controllers\Admin\Settings\TopBarSettingsController;
use Shopen\Http\Controllers\Admin\TaxClass\TaxClassCreateController;
use Shopen\Http\Controllers\Admin\TaxClass\TaxClassEditController;
use Shopen\Http\Controllers\Admin\TaxClass\TaxClassIndexController;
use Shopen\Http\Controllers\Admin\User\UserEditController;
use Shopen\Http\Controllers\Admin\User\UserIndexController;

Route::middleware(['web'])->prefix('/admin')->name('admin.')->group(function () {
    Route::get('logowanie', [LoginController::class, 'create'])->name('login');
    Route::post('logowanie', [LoginController::class, 'store']);
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');
});

Route::middleware(['web', 'admin.guard'])->prefix('/admin')->name('admin.')->group(function () {
    Route::get('', [DashboardIndexController::class, 'index'])->name('dashboard');
    Route::get('/produkty/opinie', [ProductReviewsIndexController::class, 'index'])->name('products.reviews.index');
    Route::put('/produkty/opinie/{review}/accept', [ProductReviewsEditController::class, 'accept'])->name('products.reviews.accept');
    Route::put('/produkty/opinie/{review}/reject', [ProductReviewsEditController::class, 'reject'])->name('products.reviews.reject');
    Route::delete('/produkty/opinie/{review}', [ProductReviewsEditController::class, 'destroy'])->name('products.reviews.delete');

    Route::get('/produkty/import', [ProductsImportController::class, 'index'])->name('products.import');
    Route::post('/produkty/import', [ProductsImportController::class, 'import'])->name('products.import.process');
    Route::post('/produkty/import/walidacja', [ProductsImportValidationController::class, 'validate'])->name('products.import.validate');
    Route::get('/produkty/import/walidacja', function () {return redirect(route('admin.products.import')); });
    Route::get('/produkty/export', [ProductsExportController::class, 'index'])->name('products.export');
    Route::get('/produkty/export/{filename}', [ProductsExportController::class, 'download'])->name('products.export.download');
    Route::post('/produkty/export', [ProductsExportController::class, 'export'])->name('products.export.submit');

    Route::get('/produkty', [ProductsIndexController::class, 'index'])->name('products.index');
    Route::get('/api/produkty', [ApiProductsController::class, 'index'])->name('api.products.index');
    Route::get('/produkty/nowy', [ProductCreateController::class, 'create'])->name('products.create');
    Route::get('/produkty/nowy/{product}', [ProductCreateController::class, 'duplicate'])->name('products.duplicate');
    Route::post('/produkty', [ProductCreateController::class, 'store'])->name('products.store');
    Route::post('/api/produkty', [ApiProductsController::class, 'storeVariant'])->name('products.store-variant');
    Route::get('/produkty/{product}', [ProductEditController::class, 'edit'])->name('products.edit');
    Route::put('/produkty/{product}', [ProductEditController::class, 'update'])->name('products.update');
    Route::put('/api/produkty/{product}', [ApiProductsController::class, 'updateVariant'])->name('products.update-variant');
    Route::delete('/produkty/{product}', [ProductEditController::class, 'destroy'])->name('products.delete');


    //Route::get('/produkty/reguly-cenowe/nowa', [PriceRulesController::class, 'create'])->name('products.price-rules.create');
    //Route::post('/api/products/price-rules', [ApiPriceRulesController::class, 'store'])->name('api.products.price-rules.store');

    Route::post('/api/upload-image', [UploadController::class, 'uploadImage'])->name('api.products.upload-image');

    Route::get('/kategorie', [CategoriesIndexController::class, 'index'])->name('categories.index');
    Route::get('/kategorie/nowa', [CategoryCreateController::class, 'create'])->name('categories.create');
    Route::post('/kategorie/{category?}', [CategoryCreateController::class, 'store'])->name('categories.store');
    Route::get('/kategorie/{category}/nowa', [CategoryCreateController::class, 'create'])->name('categories.create-subcategory');
    Route::get('/kategorie/{id}', [CategoryEditController::class, 'edit'])->name('categories.edit');
    Route::put('/kategorie/{category}', [CategoryEditController::class, 'update'])->name('categories.update');
    Route::put('/kategorie/{category}/sort-index', [CategoryEditController::class, 'move'])->name('categories.move');
    Route::delete('/kategorie/{category}', [CategoryEditController::class, 'destroy'])->name('categories.delete');

    Route::get('/zamowienia', [OrdersIndexController::class, 'index'])->name('orders.index');
    Route::get('/zamowienia/{order}', [OrderShowController::class, 'show'])->name('orders.show');
    Route::post('/zamowienia/{order}/status', [OrderShowController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('/zamowienia/{order}/wysylka', [OrderShowController::class, 'updateShipping'])->name('orders.shipping');
    Route::post('/zamowienia/{order}/platnosc/{payment}', [OrderShowController::class, 'updatePaymentStatus'])->name('orders.update-payment-status');
    Route::post('/zamowienia/{order}/wyslij-bony', [OrderShowController::class, 'sendVouchersEmail'])->name('orders.send-vouchers');
    Route::get('/api/orders', [ApiOrdersController::class, 'index']);

    Route::get('/kody-promocyjne', [PromoCodesIndexController::class, 'index'])->name('promo-codes.index');
    Route::get('/kody-promocyjne/nowy', [PromoCodeCreateController::class, 'create'])->name('promo-codes.create');
    Route::post('/kody-promocyjne', [PromoCodeCreateController::class, 'store'])->name('promo-codes.store');
    Route::get('/kody-promocyjne/{promoCode}/edycja', [PromoCodeEditController::class, 'edit'])->name('promo-codes.edit');
    Route::put('/kody-promocyjne/{promoCode}', [PromoCodeEditController::class, 'update'])->name('promo-codes.update');
    Route::delete('/kody-promocyjne/{promoCode}', [PromoCodeEditController::class, 'destroy'])->name('promo-codes.delete');

    Route::get('/bannery', [BannersIndexController::class, 'index'])->name('banners.index');
    Route::get('/bannery/nowy', [BannerCreateController::class, 'create'])->name('banners.create');
    Route::post('/bannery', [BannerCreateController::class, 'store'])->name('banners.store');
    Route::get('/bannery/{banner}/edycja', [BannerEditController::class, 'edit'])->name('banners.edit');
    Route::put('/bannery/{banner}', [BannerEditController::class, 'update'])->name('banners.update');
    Route::delete('/bannery/{banner}', [BannerEditController::class, 'destroy'])->name('banners.delete');

    Route::get('/atrybuty', [AttributeIndexController::class, 'index'])->name('attributes.index');
    Route::get('/atrybuty/nowy', [AttributeCreateController::class, 'create'])->name('attributes.create');
    Route::post('/atrybuty', [AttributeCreateController::class, 'store'])->name('attributes.store');
    Route::get('/atrybuty/{attribute}', [AttributeEditController::class, 'edit'])->name('attributes.edit');
    Route::put('/atrybuty/{attribute}', [AttributeEditController::class, 'update'])->name('attributes.update');
    Route::delete('/atrybuty/{attribute}', [AttributeEditController::class, 'destroy'])->name('attributes.delete');

    Route::get('/wiadomosci', [ContactMessageIndexController::class, 'index'])->name('contact-messages.index');
    Route::get('/wiadomosci/{contactMessage}', [ContactMessageShowController::class, 'show'])->name('contact-messages.show');
    Route::put('/wiadomosci/{contactMessage}', [ContactMessageEditController::class, 'update'])->name('contact-messages.update');
    Route::post('/wiadomosci/{contactMessage}/respond', [ContactMessageEditController::class, 'respond'])->name('contact-messages.respond');
    Route::delete('/wiadomosci/{contactMessage}', [ContactMessageEditController::class, 'destroy'])->name('contact-messages.delete');

    Route::get('/uzytkownicy', [UserIndexController::class, 'index'])->name('users.index');
    Route::get('/uzytkownicy/{user}', [UserEditController::class, 'edit'])->name('users.edit');

    Route::get('/marki', [BrandIndexController::class, 'index'])->name('brands.index');
    Route::post('/marki', [BrandCreateController::class, 'store'])->name('brands.store');
    Route::get('/marki/nowa', [BrandCreateController::class, 'create'])->name('brands.create');
    Route::get('/marki/{brand:id}', [BrandEditController::class, 'edit'])->name('brands.edit');
    Route::post('/marki/{brand:id}', [BrandEditController::class, 'update'])->name('brands.update');
    Route::delete('/marki/{brand:id}', [BrandEditController::class, 'destroy'])->name('brands.delete');

    Route::get('/stawki-podatkowe', [TaxClassIndexController::class, 'index'])->name('tax-classes.index');
    Route::post('/stawki-podatkowe', [TaxClassCreateController::class, 'store'])->name('tax-classes.store');
    Route::get('/stawki-podatkowe/nowa', [TaxClassCreateController::class, 'create'])->name('tax-classes.create');
    Route::get('/stawki-podatkowe/{taxClass}', [TaxClassEditController::class, 'edit'])->name('tax-classes.edit');
    Route::put('/stawki-podatkowe/{taxClass}', [TaxClassEditController::class, 'update'])->name('tax-classes.update');
    Route::delete('/stawki-podatkowe/{taxClass}', [TaxClassEditController::class, 'destroy'])->name('tax-classes.delete');

    Route::post('/cache/clear', [CacheController::class, 'clear'])->name('cache.clear');

    Route::prefix('/ustawienia')->name('settings.')->group(function () {
        Route::get('/belka', [TopBarSettingsController::class, 'index'])->name('top-bar.index');
        Route::put('/belka', [TopBarSettingsController::class, 'update'])->name('top-bar.update');


        Route::get('/instagram', [InstagramSettingsController::class, 'index'])->name('instagram.index');
        Route::put('/instagram', [InstagramSettingsController::class, 'update'])->name('instagram.update');
        Route::get('/instagram/callback', [InstagramSettingsController::class, 'callback'])->name('instagram.callback');
    });

    Route::post('/api/upload-image', [UploadController::class, 'uploadImage']);


});
