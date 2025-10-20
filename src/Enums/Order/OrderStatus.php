<?php

namespace Shopen\Enums\Order;

enum OrderStatus: string
{
    case NEW = 'new';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case READY_FOR_PICKUP = 'ready_for_pickup';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';

    public function label(): string
    {
        return match($this) {
            self::NEW => 'Nowe',
            self::PROCESSING => 'W trakcie realizacji',
            self::SHIPPED => 'Wysłane',
            self::DELIVERED => 'Dostarczono',
            self::READY_FOR_PICKUP => 'Gotowe do odbioru',
            self::CANCELLED => 'Anulowano',
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
