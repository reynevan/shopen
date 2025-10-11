<?php

namespace Shopen\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Shopen\Models\ShoppingList\ShoppingList;

class ShoppingListService
{

    private ?Collection $userLists = null;

    private ?array $productToListsMap = null;

    public function getCurrentUserListsQuery()
    {
        if (Auth::check()) {
            return ShoppingList::query()->where('user_id', Auth::id());
        }

        return ShoppingList::query()->where('session_id', $this->getGuestSessionId());
    }

    public function loadUserShoppingLists(): Collection
    {
        if (!is_null($this->userLists)) {
            return $this->userLists;
        }

        $this->userLists = $this->getCurrentUserListsQuery()
            ->with('products:id')
            ->get();

        $this->buildProductToListsMap();

        return $this->userLists;
    }

    public function isProductOnAnyList(int $productId): bool
    {
        $this->ensureListsAreLoaded();

        return isset($this->productToListsMap[$productId]);
    }

    public function getProductListIds(int $productId): array
    {
        $this->ensureListsAreLoaded();

        return $this->productToListsMap[$productId] ?? [];
    }

    public function getProductListId(int $productId): ?int
    {
        return $this->getProductListIds($productId)[0] ?? null;
    }

    private function ensureListsAreLoaded(): void
    {
        if (is_null($this->userLists)) {
            $this->loadUserShoppingLists();
        }
    }

    private function buildProductToListsMap(): void
    {
        $this->productToListsMap = [];
        if ($this->userLists->isEmpty()) {
            return;
        }

        foreach ($this->userLists as $list) {
            foreach ($list->products as $product) {
                // Inicjalizuj tablicę, jeśli to pierwszy raz dla tego produktu
                if (!isset($this->productToListsMap[$product->id])) {
                    $this->productToListsMap[$product->id] = [];
                }
                // Dodaj ID listy do tablicy dla danego produktu
                $this->productToListsMap[$product->id][] = $list->id;
            }
        }
    }

    public function findOrCreateListByName(string $name): ShoppingList
    {
        $query = $this->getCurrentUserListsQuery();
        $list = $query->firstWhere('name', $name);

        if ($list) {
            return $list;
        }

        $attributes = ['name' => $name];
        if (Auth::check()) {
            $attributes['user_id'] = Auth::id();
        } else {
            $attributes['session_id'] = $this->getGuestSessionId();
        }

        $this->userLists = null;
        $this->productToListsMap = null;

        return ShoppingList::create($attributes);
    }

    public function getGuestSessionId(): string
    {
        if (!Session::has('shopping_list_session_id')) {
            Session::put('shopping_list_session_id', Str::uuid()->toString());
        }
        return Session::get('shopping_list_session_id');
    }

    public function mergeGuestListsToUser(int $userId, string $guestSessionId): void
    {
        if ($guestSessionId) {
            ShoppingList::where('session_id', $guestSessionId)
                ->update([
                    'user_id' => $userId,
                    'session_id' => null,
                ]);
        }
    }

    public function removeProductFromList($productId, $listId)
    {

    }
}