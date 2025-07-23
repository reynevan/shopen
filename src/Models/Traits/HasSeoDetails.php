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

    public function getSeoForWebsite(int $websiteId, bool $generateDefault = false): ?SeoDetail
    {
        $seoDetails = $this->seoDetails()->where('website_id', $websiteId)->first();
        if ($seoDetails || !$generateDefault) {
            return $seoDetails;
        }
        return new SeoDetail([
            'seo_title' => $this->getDefaultSeoTitle(),
            'seo_description' => $this->getDefaultSeoDescription()
        ]);
    }

    public function createOrUpdateSeoForWebsite(int $websiteId, array $data): SeoDetail
    {
        return $this->seoDetails()->updateOrCreate(
            [
                'website_id' => $websiteId,
            ],
            [
                'seo_title' => $data['seo_title'] ?? null,
                'seo_description' => $data['seo_description'] ?? null,
            ]
        );
    }
}