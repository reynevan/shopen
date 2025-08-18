<?php

namespace Shopen\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Shopen\Enums\Address\AddressType;
use Shopen\Models\Address;
use Shopen\Models\User;

class AddressRepository
{
    public function syncDefaultAddresses(Address $address)
    {
        if ($address->user && $address->is_default) {
            $address
                ->user
                ->addresses()
                ->whereNot('id', $address->id)
                ->where('type', $address->type)
                ->update(['is_default' => 0]);
        }
    }

    public function selectFirstDefaultAddress(User $user, $type)
    {
        $user->addresses()
            ->where('type', $type)
            ->oldest()
            ->limit(1)
            ->update(['is_default' => 1]);

    }

    public function getUserShippingAddresses(User $user, $withDefault = true): Collection
    {
        return $this->getUserAddresses($user, AddressType::SHIPPING, $withDefault);
    }

    public function getUserBillingAddresses(User $user, $withDefault = true): Collection
    {
        return $this->getUserAddresses($user, AddressType::BILLING, $withDefault);
    }

    protected function getUserAddresses(User $user, AddressType $type, $withDefault): Collection
    {
        return Address::query()
            ->where('user_id', $user->id)
            ->where('type', $type)
            ->when(!$withDefault, function (Builder $query) {
                $query->where('is_default', false);
            })
            ->orderBy('is_default', 'desc')
            ->latest()
            ->get();
    }
}