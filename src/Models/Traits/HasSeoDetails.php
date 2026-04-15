<?php

namespace Shopen\Models\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Shopen\Models\SeoDetail;

trait HasSeoDetails
{

    protected function getDefaultSeoTitle(): string
    {
        return '';
    }

    protected function getDefaultSeoDescription(): string
    {
        return '';
    }

    public function seoDetails(): MorphMany
    {
        return $this->morphMany(SeoDetail::class, 'seoable');
    }

    public function getSeoForStore(int $storeId, bool $generateDefault = false): ?SeoDetail
    {
        $seoDetails = $this->seoDetails()->where('store_id', $storeId)->first();
        if ($seoDetails || !$generateDefault) {
            return $seoDetails;
        }
        return new SeoDetail([
            'seo_title' => $this->getDefaultSeoTitle(),
            'seo_description' => $this->getDefaultSeoDescription()
        ]);
    }

    public function createOrUpdateSeoForStore(int $storeId, array $data): SeoDetail
    {
        return $this->seoDetails()->updateOrCreate(
            [
                'store_id' => $storeId,
            ],
            [
                'seo_title' => $data['seo_title'] ?? null,
                'seo_description' => $data['seo_description'] ?? null,
            ]
        );
    }
}