<?php

namespace Shopen\Models\Cart;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Shopen\Enums\Address\AddressType;
use Shopen\Models\Address;

class CartAddress extends Address
{
    use HasFactory;

    protected $fillable = [
        'address_id',
        'cart_id',
        'type',
        'first_name',
        'last_name',
        'company',
        'company_nip',
        'address_line',
        'city',
        'postal_code',
        'country',
        'phone',
        'email',
    ];

    protected $casts = [
        'type' => AddressType::class,
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getFormattedAddressAttribute(): string
    {
        $address = $this->address_line;
        $address .= ', ' . $this->city . ', ' . $this->state . ' ' . $this->postal_code;
        $address .= ', ' . $this->country;

        return $address;
    }
}