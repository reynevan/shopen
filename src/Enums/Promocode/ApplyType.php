<?php

namespace Shopen\Enums\Promocode;

enum ApplyType: string
{
    case CART = 'cart';
    case PER_ITEM = 'per_item';

    public function label(): string
    {
        return match($this) {
            self::CART => 'Cały koszyk',
            self::PER_ITEM => 'Każdy produkt osobno'
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
