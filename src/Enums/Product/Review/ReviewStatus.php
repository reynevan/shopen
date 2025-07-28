<?php

namespace Shopen\Enums\Product\Review;

enum ReviewStatus: string
{
    case PENDING = 'pending';
    case PENDING_EDIT = 'pending_edit';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Oczekuje',
            self::PENDING_EDIT => 'Edytowano',
            self::APPROVED => 'Zaakceptowano',
            self::REJECTED => 'Odrzucono'
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }

    public static function values(): array
    {
        return collect(self::cases())->toArray();
    }
}
