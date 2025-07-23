<?php

namespace Shopen\Enums\Banner;

enum PlacementType: string
{
    case PREDEFINED = 'predefined';
    case DYNAMIC = 'dynamic';

    public function label(): string
    {
        return match($this) {
            self::PREDEFINED => 'Stałe miejsce',
            self::DYNAMIC => 'Dynamiczne',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
