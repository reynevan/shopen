<?php

namespace Shopen\Models\TextSlide;

use Illuminate\Database\Eloquent\Model;

class TextSlide extends Model
{
    protected $fillable = [
        'content',
        'color',
        'background_color'
    ];
}
