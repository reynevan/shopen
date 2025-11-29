<?php

namespace Shopen\Models\Instagram;

use Illuminate\Database\Eloquent\Model;

class InstagramPost extends Model
{
    protected $fillable = [
        'media_id',
        'media_url',
        'post_url',
        'timestamp'
    ];
}
