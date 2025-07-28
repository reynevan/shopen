<?php

namespace Shopen\Models\Product;

use Illuminate\Database\Eloquent\Model;

class UserProductView extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'product_id', 'viewed_at'];
}
