<?php

namespace Shopen\Listeners;

use Shopen\Services\ShoppingListService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Session;

class MergeGuestShoppingLists
{
    public function __construct(protected ShoppingListService $shoppingListService)
    {}

    public function handle(object $event): void
    {
        if (Session::has('shopping_list_session_id')) {
            $guestSessionId = Session::get('shopping_list_session_id');
            $user = $event->user;

            $this->shoppingListService->mergeGuestListsToUser($user->getAuthIdentifier(), $guestSessionId);

            Session::forget('shopping_list_session_id');
        }
    }
}