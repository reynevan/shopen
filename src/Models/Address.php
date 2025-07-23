<?php

namespace Shopen\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Shopen\Enums\Address\AddressType;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'company',
        'address_line',
        'city',
        'postal_code',
        'country',
        'phone',
        'is_default',
        'email',
        'type'
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'type' => AddressType::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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

    public function getIsBillingAttribute()
    {
        return $this->type === AddressType::BILLING;
    }

    public function getIsShippingAttribute()
    {
        return $this->type === AddressType::SHIPPING;
    }
}