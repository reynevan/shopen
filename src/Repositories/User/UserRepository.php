<?php

namespace Shopen\Repositories\User;

use Illuminate\Database\Eloquent\Builder;
use Shopen\Models\User;

class UserRepository
{


    public function getPaginated($sortField, $sortDir, $searchQuery = null)
    {
        return User::query()
            ->customers()
            ->when($searchQuery, function (Builder $query) use ($searchQuery) {
                $query
                    ->whereLike('first_name', '%' . $searchQuery . '%')
                    ->orWhereLike('last_name', '%' . $searchQuery . '%')
                    ->orWhereLike('email', '%' . $searchQuery . '%');
            })
            ->orderBy($sortField, $sortDir)
            ->paginate(25)
            ->withQueryString();
    }
}