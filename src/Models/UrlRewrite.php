<?php

namespace Shopen\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class UrlRewrite extends Model
{
    public function entity(): MorphTo
    {
        return $this->morphTo();
    }
}
