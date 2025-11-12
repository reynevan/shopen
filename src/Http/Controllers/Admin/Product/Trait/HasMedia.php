<?php

namespace Shopen\Http\Controllers\Admin\Product\Trait;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Shopen\Models\Product\Product;

trait HasMedia
{

    protected function updateImages(Product $product, array $imagesData): void
    {
        $this->deleteRemovedImages($product, $imagesData);
        $this->processImages($product, $this->sortImagesByOrder($imagesData));
    }

    private function deleteRemovedImages(Product $product, array $imagesData): void
    {
        $existingIds = Arr::pluck($imagesData, 'id');
        $product->media()->whereNotIn('id', $existingIds)->delete();
    }

    private function sortImagesByOrder(array $imagesData): array
    {
        usort($imagesData, fn($a, $b) => ($a['order'] ?? 0) <=> ($b['order'] ?? 0));
        return $imagesData;
    }

    private function processImages(Product $product, array $imagesData): void
    {
        $order = 1;
        foreach ($imagesData as $imageData) {
            if ($this->isNewImage($imageData)) {
                $this->addNewImage($product, $imageData, $order++);
            } elseif ($this->isExistingImage($imageData)) {
                $this->updateExistingImage($product, $imageData, $order++);
            }
        }
    }

    private function isNewImage(array $imageData): bool
    {
        return ($imageData['new'] ?? false) && isset($imageData['path']);
    }

    private function isExistingImage(array $imageData): bool
    {
        return isset($imageData['id']);
    }

    private function addNewImage(Product $product, array $imageData, int $order): void
    {
        $product
            ->addMedia(Storage::disk('public')->path($imageData['path']))
            ->setOrder($order)
            ->withCustomProperties($this->getCustomProperties($imageData))
            ->toMediaCollection();
    }

    private function updateExistingImage(Product $product, array $imageData, int $order): void
    {
        $media = $product->media()->find($imageData['id']);

        if (!$media) {
            return;
        }

        foreach ($this->getCustomProperties($imageData) as $key => $value) {
            $media->setCustomProperty($key, $value);
        }

        $media->order_column = $order;
        $media->save();
    }

    private function getCustomProperties(array $imageData): array
    {
        return [
            'gallery' => $imageData['gallery'] ?? false,
            'meta' => $imageData['meta'] ?? false,
            'thumbnail' => $imageData['thumbnail'] ?? false,
        ];
    }
}