<?php

namespace Shopen\Core\Support;

use Illuminate\Database\Eloquent\Builder;

class DB
{
    public static function isJoined(Builder $query, $table): bool
    {
        $joins = collect($query->getQuery()->joins);
        return $joins->pluck('table')->contains($table);
    }
}