<?php

namespace Shopen\Http\Controllers\Frontend\Api;



use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Shopen\Enums\Address\AddressType;
use Shopen\Http\Controller;
use Shopen\Http\Requests\Frontend\User\Address\StoreBillingAddressRequest;
use Shopen\Http\Requests\Frontend\User\Address\StoreShippingAddressRequest;
use Shopen\Http\Requests\Frontend\User\Address\UpdateShippingAddressRequest;
use Shopen\Models\Address;
use Shopen\Repositories\AddressRepository;
use Shopen\Services\CartService;

class UsersController extends Controller
{
    public function __construct(
        protected readonly AddressRepository $addressRepository,
        protected readonly CartService $cartService,
    )
    {}

    public function storeShippingAddress(StoreShippingAddressRequest $request): RedirectResponse
    {
        $address = Auth::user()->addresses()->create($request->validated());
        $this->addressRepository->syncDefaultAddresses($address);

        if ($this->cartService->hasCart()) {
            $this->cartService->setAddress($address, AddressType::SHIPPING);
        }

        return back();
    }

    public function storeBillingAddress(StoreBillingAddressRequest $request): RedirectResponse
    {
        $address = Auth::user()->addresses()->create($request->validated());
        $this->addressRepository->syncDefaultAddresses($address);

        if ($this->cartService->hasCart()) {
            $this->cartService->setAddress($address, AddressType::BILLING);
        }

        return back();
    }

    public function updateShippingAddress(Address $address, UpdateShippingAddressRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $address->fill($data);
        $address->save();
        $this->addressRepository->syncDefaultAddresses($address);

        if ($this->cartService->hasCart()) {
            $this->cartService->setAddress($address, AddressType::SHIPPING);
        }

        return back();
    }

    public function updateBillingAddress(Address $address, UpdateShippingAddressRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $address->fill($data);
        $address->save();
        $this->addressRepository->syncDefaultAddresses($address);

        if ($this->cartService->hasCart()) {
            $this->cartService->setAddress($address, AddressType::BILLING);
        }

        return back();
    }
}