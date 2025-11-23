<?php

namespace Shopen\Http\Resources\Banner;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class BannerResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        $desktopMedia = $this->resource->getMedia('desktop')->first();
        $mobileMedia = $this->resource->getMedia('mobile')->first();
        // Pełne ścieżki plików w storage (disk "public")
        $desktopPath = $desktopMedia ? $desktopMedia->getPath('banner') : null;
        $mobilePath  = $mobileMedia ? $mobileMedia->getPath('banner') : null;

        // Pobranie wymiarów (szerokość, wysokość) jeśli plik istnieje
        [$desktopWidth, $desktopHeight] = $this->getImageSizeSafe($desktopPath);
        [$mobileWidth, $mobileHeight]   = $this->getImageSizeSafe($mobilePath);

        return [
            'id' => $this->id,
            'title' => $this->title,
            'alt_text' => $this->alt_text,
            'link_url' => $this->link_url,
            'opens_in_new_tab' => $this->opens_in_new_tab,

            'image_url_desktop' => $this->getDesktopFileUrl(),
            'image_url_mobile' => $this->getMobileFileUrl(),

            // Nowe pola z wymiarami
            'image_size_desktop' => $desktopWidth && $desktopHeight ? [
                'width' => $desktopWidth,
                'height' => $desktopHeight,
            ] : null,

            'image_size_mobile' => $mobileWidth && $mobileHeight ? [
                'width' => $mobileWidth,
                'height' => $mobileHeight,
            ] : null,
        ];
    }

    /**
     * Zwraca [width, height] lub [null, null] jeśli brak pliku lub błąd.
     */
    protected function getImageSizeSafe(?string $absolutePath): array
    {
        if (!$absolutePath || !is_file($absolutePath)) {
            return [null, null];
        }

        try {
            $size = @getimagesize($absolutePath);
            if ($size && isset($size[0], $size[1])) {
                return [(int) $size[0], (int) $size[1]];
            }
        } catch (\Throwable $e) {
            // opcjonalnie: zaloguj błąd
        }

        return [null, null];
    }

}
