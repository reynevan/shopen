<?php

namespace Shopen\Enums\Payment;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Oczekuje',
            self::PROCESSING => 'W trakcie',
            self::COMPLETED => 'Zakończone',
            self::FAILED => 'Błąd',
            self::CANCELLED => 'Anulowane',
            self::REFUNDED => 'Zwrócono',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
