<?php

namespace Shopen\Http\Controllers\Frontend\User;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Shopen\Repositories\AddressRepository;

class UserAddressesIndexController
{
    public function __construct(
        protected AddressRepository $addressRepository
    )
    {

    }

    public function index()
    {
        return Inertia::render('Frontend/User/Addresses/Index', [
                'defaultShippingAddress' => fn() => Auth::user()->defaultShippingAddress(true),
                'defaultBillingAddress' => fn() => Auth::user()->defaultBillingAddress(true),
                'shippingAddresses' => fn () => $this->addressRepository->getUserShippingAddresses(Auth::user(), false),
                'billingAddresses' => fn () => $this->addressRepository->getUserBillingAddresses(Auth::user(), false)
            ]
        );
    }
}