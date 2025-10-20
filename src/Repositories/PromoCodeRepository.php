<?php

namespace Shopen\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Shopen\Models\PromoCode\PromoCode;

class PromoCodeRepository
{
    public function getValidByCode($code)
    {
        $promocode = PromoCode::query()->where("code", $code)->first();
        if (!$promocode || !$promocode->isValid()) {
            return null;
        }
        return $promocode;
    }

    public function getAll()
    {
        return PromoCode::query();
    }

    public function getPaginated($sortField, $sortDir, $searchQuery = null)
    {
        return PromoCode::query()
            ->when($searchQuery, function (Builder $query) use ($searchQuery) {
                $query
                    ->whereLike('code', '%' . $searchQuery . '%')
                    ->orWhereLike('name', '%' . $searchQuery . '%')
                    ->orWhereLike('description', '%' . $searchQuery . '%');
            })
            ->orderBy($sortField, $sortDir)
            ->paginate()
            ->withQueryString();
    }
}