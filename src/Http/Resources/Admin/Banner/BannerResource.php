<?php

namespace Shopen\Http\Resources\Admin\Banner;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Shopen\Enums\Banner\Placement;

class BannerResource extends JsonResource
{

    public function toArray(Request $request): array
    {

        return [
            'id' => $this->id,
            'title' => $this->title,
            'alt_text' => $this->alt_text,
            'image_path_desktop' => $this->image_path_desktop,
            'image_url_desktop' => $this->image_path_desktop ? Storage::url($this->image_path_desktop) : null,
            'image_path_mobile' => $this->image_path_mobile,
            'image_url_mobile' => $this->image_path_mobile ? Storage::url($this->image_path_mobile) : null,
            'link_url' => $this->link_url,
            'opens_in_new_tab' => $this->opens_in_new_tab,
            'placement_type' => $this->placement_type,
            'placement_key' => $this->placement_key,
            'placement_key_label' => Placement::tryFrom($this->placement_key)?->label() ?? $this->placement_key,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
            'click_count' => $this->click_count,
            'category_ids' => $this->categories->pluck('id')->toArray(),
        ];
    }

}
