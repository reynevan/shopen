<?php

namespace Shopen\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Shopen\Enums\Address\AddressType;
use Shopen\Models\Order\Order;
use Shopen\Models\Product\Review\ProductReview;
use Shopen\Database\Factories\UserFactory;
use Shopen\Models\ShoppingList\ShoppingList;
use Shopen\Notifications\ResetPassword;

class User extends Authenticatable
{
    use Notifiable, HasFactory;

    const ROLE_USER = 'user';
    const ROLE_ADMIN = 'admin';

    protected $fillable = [
        'name',
        'role',
        'email',
        'password',
        'first_name',
        'last_name',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    protected static function newFactory()
    {
        return UserFactory::new();
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPassword($token));
    }

    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function shippingAddresses(): HasMany
    {
        return $this->hasMany(Address::class)->where('type', AddressType::SHIPPING)->orderByDesc('is_default');
    }

    public function billingAddresses(): HasMany
    {
        return $this->hasMany(Address::class)->where('type', AddressType::BILLING)->orderByDesc('is_default');
    }

    public function reviews() {
        return $this->hasMany(ProductReview::class);
    }

    public function shoppingLists(): HasMany
    {
        return $this->hasMany(ShoppingList::class);
    }

    public function defaultShippingAddress($strict = false)
    {
        return $this
            ->addresses()
            ->where('type', AddressType::SHIPPING)
            ->when($strict, function (Builder $query) {
                $query->where('is_default', true);
            })
            ->orderByDesc('is_default')
            ->first();
    }

    public function defaultBillingAddress($strict = false)
    {
        return $this
            ->addresses()
            ->where('type', AddressType::BILLING)
            ->when($strict, function (Builder $query) {
                $query->where('is_default', true);
            })
            ->orderByDesc('is_default')
            ->first();
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function scopeCustomers($query)
    {
        return $query->where('role', self::ROLE_USER);
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', self::ROLE_ADMIN);
    }
}
