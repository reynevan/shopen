<?php

namespace Shopen\Http\Controllers\Admin\User;

use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Requests\Admin\User\Address\StoreAddressRequest;
use Shopen\Http\Requests\Admin\User\Address\UpdateAddressRequest;
use Shopen\Http\Resources\Admin\User\CustomerResource;
use Shopen\Http\Resources\Order\OrderResource;
use Shopen\Http\Resources\User\AddressResource;
use Shopen\Models\Address;
use Shopen\Models\User;
use Shopen\Repositories\AddressRepository;

class UserEditController
{
    public function __construct(
        protected AddressRepository $addressRepository
    )
    {}

    public function edit(User $user): Response
    {
        $orders = $user->orders()->latest()->get();
        $user->load(['shippingAddresses', 'billingAddresses']);

        $defaultShippingAddress = $user->defaultShippingAddress(true);
        $defaultBillingAddress = $user->defaultBillingAddress(true);

        return Inertia::render('Admin/User/Edit', [
            'user' => fn () => CustomerResource::make($user),
            'orders' => fn () => OrderResource::collection($orders),
            'defaultShippingAddress' => fn () => $defaultShippingAddress ? AddressResource::make($defaultShippingAddress) : null,
            'defaultBillingAddress' => fn () => $defaultBillingAddress ? AddressResource::make($defaultBillingAddress) : null,
            'shippingAddresses' => fn () => AddressResource::collection($this->addressRepository->getUserShippingAddresses($user, false)),
            'billingAddresses' => fn () => AddressResource::collection($this->addressRepository->getUserBillingAddresses($user, false)),
            'tab' => fn () => request('tab')
        ]);

    }

    public function storeAddress(StoreAddressRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        $address = $user->addresses()->create($data);
        $this->addressRepository->syncDefaultAddresses($address);
        return back()->with('success', 'Adres został zapisany');
    }

    public function updateAddress(UpdateAddressRequest $request, User $user, Address $address): RedirectResponse
    {
        $data = $request->validated();
        $address->update($data);
        $this->addressRepository->syncDefaultAddresses($address);
        return back()->with('success', 'Adres został zapisany');
    }

    public function destroyAddress(User $user, Address $address): RedirectResponse
    {
        $address->delete();
        return back()->with('success', 'Adres został usunięty');
    }
}