<?php

namespace Shopen\Models\ShoppingList;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Shopen\Models\Product\Product;
use Shopen\Models\User;

class ShoppingList extends Model
{
    protected $fillable = ['user_id', 'session_id', 'name'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'shopping_list_product');
    }
}
