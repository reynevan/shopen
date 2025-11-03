<?php

namespace Shopen\Enums\ContactMessage;

enum Status: string
{
    case NEW = 'new';
    case READ = 'read';
    case REPLIED = 'replied';

    public function label(): string
    {
        return match($this) {
            self::NEW => 'Nowa',
            self::READ => 'Odczytana',
            self::REPLIED => 'Wysłano odpowiedź',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
