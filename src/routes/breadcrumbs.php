<?php

use Shopen\Facades\Breadcrumbs;
use Shopen\Services\BreadcrumbsService;

Breadcrumbs::register('login', function (BreadcrumbsService $breadcrumbs) {
    $breadcrumbs->add('Logowanie', route('login'));
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