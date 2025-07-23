<?php

namespace Shopen\Enums\Address;

enum AddressType: string
{
    case BILLING = 'billing';
    case SHIPPING = 'shipping';

    public function label(): string
    {
        return match($this) {
            self::BILLING => 'Adres wysyłki',
            self::SHIPPING => 'Dane do płatności',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
