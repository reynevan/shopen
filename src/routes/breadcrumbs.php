<?php

use Shopen\Facades\Breadcrumbs;
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

Breadcrumbs::register('shopping-lists.index', function (BreadcrumbsService $breadcrumbs) {
    $breadcrumbs->add('Listy zakupowe', route('shopping-lists.index'));
});

Breadcrumbs::register('checkout.success', function (BreadcrumbsService $breadcrumbs, $order) {
    $breadcrumbs->remove();
});