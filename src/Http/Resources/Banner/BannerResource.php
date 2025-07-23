<?php

namespace Shopen\Http\Resources\Banner;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

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
        ];
    }

}
