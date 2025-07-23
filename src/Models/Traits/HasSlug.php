<?php

namespace Shopen\Models\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    protected function slugAttribute(): string {
        return 'name';
    }

    public function getSlugAttribute()
    {
        return $this->id . '-' . Str::slug($this->getAttribute($this->slugAttribute()));
    }

}