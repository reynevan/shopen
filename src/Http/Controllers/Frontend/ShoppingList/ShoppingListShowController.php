<?php

namespace Shopen\Http\Controllers\Frontend\ShoppingList;

use Shopen\Models\ShoppingList\ShoppingList;
use Shopen\Services\ShoppingListService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Shopen\Http\Resources\ShoppingList\ShoppingListResource;

class ShoppingListShowController
{
    public function __construct(protected ShoppingListService $listService)
    {
        // Tutaj można dodać policy dla autoryzacji
    }

    public function show(ShoppingList $shoppingList)
    {
        return Inertia::render('Frontend/User/ShoppingList/Show', [
            'list' => ShoppingListResource::make($shoppingList),
        ]);
    }
}