<?php

namespace Shopen\Models\Config;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $fillable = [
        'path',
        'value',
    ];
}
