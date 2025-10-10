<?php

namespace Shopen\Models\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    public static function getIdFromSlug($slug): int
    {
        $parts = explode('-', $slug);
        return (int)$parts[0];
    }

    protected function slugAttribute(): string {
        return 'name';
    }

    public function getSlugAttribute()
    {
        return $this->id . '-' . Str::slug($this->getAttribute($this->slugAttribute()));
    }

}