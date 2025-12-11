<?php

use Shopen\Facades\Breadcrumbs;
use Shopen\Models\Brand\Brand;
use Shopen\Services\BreadcrumbsService;

Breadcrumbs::register('login', function (BreadcrumbsService $breadcrumbs) {
    $breadcrumbs->add('Logowanie', route('login'));
});

Breadcrumbs::register('register', function (BreadcrumbsService $breadcrumbs) {
    $breadcrumbs->add('Rejestracja', route('register'));
});

Breadcrumbs::register('cart.index', function (BreadcrumbsService $breadcrumbs) {
    $breadcrumbs->add('Koszyk', route('cart.index'));
});

Breadcrumbs::register('checkout.index', function (BreadcrumbsService $breadcrumbs) {
    $breadcrumbs->add('Zamówienie', route('checkout.index'));
});

Breadcrumbs::register('user.addresses.index', function (BreadcrumbsService $breadcrumbs) {
    $breadcrumbs->add('Dane do zamówień', route('user.addresses.index'));
});

Breadcrumbs::register('user.orders.index', function (BreadcrumbsService $breadcrumbs) {
    $breadcrumbs->add('Moje zamówienia', route('user.orders.index'));
});

Breadcrumbs::register('user.orders.show', function (BreadcrumbsService $breadcrumbs, $order) {
    $breadcrumbs->add('Moje zamówienia', route('user.orders.index'));
    $breadcrumbs->add('Zamówienie #' . $order->order_number, route('user.orders.show', $order));
});

Breadcrumbs::register('user.shopping-lists.index', function (BreadcrumbsService $breadcrumbs) {
    $breadcrumbs->add('Listy zakupowe', route('user.shopping-lists.index'));
});

Breadcrumbs::register('user.settings.index', function (BreadcrumbsService $breadcrumbs) {
    $breadcrumbs->add('Ustawienia konta', route('user.settings.index'));
});

Breadcrumbs::register('user.shopping-lists.show', function (BreadcrumbsService $breadcrumbs, $shoppingList) {
    $breadcrumbs->add('Listy zakupowe', route('user.shopping-lists.index'));
    $breadcrumbs->add($shoppingList->name, route('user.shopping-lists.show', $shoppingList->id));
});

Breadcrumbs::register('checkout.success', function (BreadcrumbsService $breadcrumbs, $order) {
    $breadcrumbs->remove();
});

Breadcrumbs::register('contact.index', function (BreadcrumbsService $breadcrumbs) {
    $breadcrumbs->add('Kontakt', route('contact.index'));
});

Breadcrumbs::register('search.index', function (BreadcrumbsService $breadcrumbs) {
    $breadcrumbs->add('Wyniki wyszukiwania', route('search.index'));
});

Breadcrumbs::register('terms-and-conditions', function (BreadcrumbsService $breadcrumbs) {
    $breadcrumbs->add('Regulamin', route('terms-and-conditions'));
});

Breadcrumbs::register('shipping', function (BreadcrumbsService $breadcrumbs) {
    $breadcrumbs->add('Dostawy', route('shipping'));
});

Breadcrumbs::register('brands.show', function (BreadcrumbsService $breadcrumbs, Brand $brand) {
    $breadcrumbs->add($brand->name, route('brands.show', $brand));
});