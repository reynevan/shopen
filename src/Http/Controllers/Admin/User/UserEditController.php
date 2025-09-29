<?php

namespace Shopen\Http\Controllers\Admin\User;

use Inertia\Inertia;
use Shopen\Http\Resources\Admin\User\CustomerResource;
use Shopen\Http\Resources\Order\OrderResource;
use Shopen\Http\Resources\User\AddressResource;
use Shopen\Models\User;
use Shopen\Repositories\AddressRepository;

class UserEditController
{
    public function __construct(
        protected AddressRepository $addressRepository
    )
    {}

    public function edit(User $user)
    {

        $orders = $user->orders()->latest()->get();
        $user->load(['shippingAddresses', 'billingAddresses']);

        $defaultShippingAddress = $user->defaultShippingAddress(true);
        $defaultBillingAddress = $user->defaultBillingAddress(true);

        return Inertia::render('Admin/User/Edit', [
            'user' => CustomerResource::make($user),
            'orders' => OrderResource::collection($orders),
            'defaultShippingAddress' => $defaultShippingAddress ? AddressResource::make($defaultShippingAddress) : null,
            'defaultBillingAddress' => $defaultBillingAddress ? AddressResource::make($defaultBillingAddress) : null,
            'shippingAddresses' => AddressResource::collection($this->addressRepository->getUserShippingAddresses($user, false)),
            'billingAddresses' => AddressResource::collection($this->addressRepository->getUserBillingAddresses($user, false))
        ]);

    }
}