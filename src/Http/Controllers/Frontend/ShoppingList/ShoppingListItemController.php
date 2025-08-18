<?php

namespace Shopen\Http\Controllers\Frontend\ShoppingList;

use Illuminate\Http\Request;
use Shopen\Models\Product\Product;
use Shopen\Models\ShoppingList\ShoppingList;
use Shopen\Services\ShoppingListService;

class ShoppingListItemController
{
    public function __construct(protected ShoppingListService $listService)
    {
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'new_list_name' => 'nullable|string|max:255',
            'list_ids' => 'nullable|array',
            'list_ids.*' => 'exists:shopping_lists,id'
        ]);

        if (!empty($validated['list_ids'])) {
            foreach ($validated['list_ids'] as $listId) {
                $list = $this->listService->getCurrentUserListsQuery()->where('id', $listId)->first();
                if (!$list) {
                    continue;
                }
                $list->products()->syncWithoutDetaching($validated['product_id']);
            }
        } else {
            $listName = $validated['new_list_name'] ?? 'Ulubione';
            $list = $this->listService->findOrCreateListByName($listName);
            $list->products()->syncWithoutDetaching($validated['product_id']);

        }
        return back()->with('success', 'Produkt dodano do listy!');
    }

    public function destroy(ShoppingList $shoppingList, Product $product)
    {

        $shoppingList->products()->detach($product->id);
        return back()->with('success', 'Produkt został usunięty z listy.');
    }
}