<?php

namespace Shopen\Http\Controllers\Frontend\ShoppingList;

use Shopen\Models\ShoppingList\ShoppingList;
use Shopen\Services\ShoppingListService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Shopen\Http\Resources\ShoppingList\ShoppingListResource;

class ShoppingListIndexController
{
    public function __construct(protected ShoppingListService $listService)
    {
        // Tutaj można dodać policy dla autoryzacji
    }

    public function index()
    {
        $lists = $this->listService->getCurrentUserListsQuery()
            ->with(['products', 'products.price', 'products.media'])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Frontend/User/ShoppingList/Index', [
            'lists' => ShoppingListResource::collection($lists),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate(['name' => 'required|string|max:255']);
        $this->listService->findOrCreateListByName($validated['name']);

        return back()->with('success', 'Lista została utworzona.');
    }

    public function update(Request $request, ShoppingList $shopping_list)
    {
        // TODO: Dodać autoryzację (Policy)
        $validated = $request->validate(['name' => 'required|string|max:255']);
        $shopping_list->update($validated);

        return back()->with('success', 'Nazwa listy została zmieniona.');
    }

    public function destroy(ShoppingList $shopping_list)
    {
        // TODO: Dodać autoryzację (Policy)
        $shopping_list->delete();
        return redirect()->route('shopping-lists.index')->with('success', 'Lista została usunięta.');
    }
}