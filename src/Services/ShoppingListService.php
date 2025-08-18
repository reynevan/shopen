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
            return ShoppingList::where('user_id', Auth::id());
        }

        $sessionId = $this->getGuestSessionId();
        return ShoppingList::where('session_id', $sessionId);
    }

    public function loadUserShoppingLists(): Collection
    {
        // Jeśli listy zostały już załadowane w tym żądaniu, zwróć je.
        if (!is_null($this->userLists)) {
            return $this->userLists;
        }

        // Pobierz wszystkie listy użytkownika z ich produktami.
        // Używamy eager loading z `with()`, aby uniknąć problemu N+1.
        // Wybieramy tylko kolumnę 'id' z tabeli produktów, bo tylko ona jest nam potrzebna do mapowania.
        $this->userLists = $this->getCurrentUserListsQuery()
            ->with('products:id')
            ->get();

        // Po pobraniu list, zbuduj mapę do szybkich zapytań.
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
        // ... (bez zmian)
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

        // Po utworzeniu nowej listy, musimy zresetować załadowane dane,
        // aby przy następnym zapytaniu zostały pobrane na nowo.
        $this->userLists = null;
        $this->productToListsMap = null;

        return ShoppingList::create($attributes);
    }

    public function getGuestSessionId(): string
    {
        // ... (bez zmian)
        if (!Session::has('shopping_list_session_id')) {
            Session::put('shopping_list_session_id', Str::uuid()->toString());
        }
        return Session::get('shopping_list_session_id');
    }

    public function mergeGuestListsToUser(int $userId, string $guestSessionId): void
    {
        // ... (bez zmian)
        if ($guestSessionId) {
            ShoppingList::where('session_id', $guestSessionId)
                ->update([
                    'user_id' => $userId,
                    'session_id' => null,
                ]);
        }
    }
}