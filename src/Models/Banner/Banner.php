<?php

namespace Shopen\Models\Banner;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Shopen\Enums\Banner\Placement;
use Shopen\Enums\Banner\PlacementType;
use Shopen\Models\Category\Category;

class Banner extends Model
{
    protected $casts = [
        'opens_in_new_tab' => 'boolean',
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'placement_type' => PlacementType::class
    ];

    protected $fillable = [
        'title',
        'alt_text',
        'image_path_desktop',
        'image_path_mobile',
        'link_url',
        'opens_in_new_tab',
        'placement_type',
        'placement_key',
        'start_date',
        'end_date',
        'is_active',
        'sort_order',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }


    public function getPagePlacementKey(): string
    {
        $placementsPageTop = [
            Placement::ALL_PAGE_TOP->value,
            Placement::HOME_PAGE_TOP->value,
            Placement::CATEGORY_PAGE_TOP->value,
            Placement::PRODUCT_PAGE_TOP->value,
        ];
        if (in_array($this->placement_key, $placementsPageTop)) {
            return 'page_top';
        }
        $placementsPageBottom = [
            Placement::ALL_PAGE_BOTTOM->value,
            Placement::HOME_PAGE_BOTTOM->value,
            Placement::CATEGORY_PAGE_BOTTOM->value,
            Placement::PRODUCT_PAGE_BOTTOM->value,
        ];
        if (in_array($this->placement_key, $placementsPageBottom)) {
            return 'page_bottom';
        }

        return $this->placement_key;
    }
}
