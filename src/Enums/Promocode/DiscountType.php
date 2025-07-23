<?php

namespace Shopen\Enums\Promocode;

enum DiscountType: string
{
    case PERCENTAGE = 'percentage';
    case FIXED = 'fixed_amount';

    public function label(): string
    {
        return match($this) {
            self::PERCENTAGE => 'Procentowa',
            self::FIXED => 'Stała kwota'
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
