<?php

namespace Shopen\Jobs;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Shopen\Models\Product\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Shopen\Repositories\Product\ProductAttributeRepository;

class ExportProducts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $fileName;
    protected $filePath;

    public function __construct()
    {
        // Generowanie nazwy pliku z hashem dla bezpieczeństwa
        $hash = Str::random(32);
        $timestamp = now()->format('Y-m-d_H-i-s');
        $this->fileName = "products_export_{$timestamp}_{$hash}.csv";
        $this->filePath = "exports/{$this->fileName}";
    }

    public function handle(ProductAttributeRepository $attributeRepository): void
    {
        // Pobranie wszystkich produktów z relacjami
        $products = Product::with([
            'price',
            'categories',
            'taxClass',
            'parent',
            'urlRewrite',
            'brand'
        ])->get();

        // Pobranie wszystkich atrybutów
        $attributes = $attributeRepository->getAll();

        // Przygotowanie nagłówków CSV
        $headers = $this->prepareHeaders($attributes);

        // Ścieżka folderu
        $directory = storage_path('app/public/exports');

        // Sprawdzenie czy folder istnieje, a jeśli nie, to jego utworzenie
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true); // rekursyjne tworzenie
        }

        // Otwarcie pliku do zapisu
        $handle = fopen(storage_path("app/public/{$this->filePath}"), 'w');

        // Zapisanie nagłówków
        fputcsv($handle, $headers);

        // Eksport każdego produktu
        foreach ($products as $product) {
            $row = $this->prepareProductRow($product, $attributes);
            fputcsv($handle, $row);
        }

        fclose($handle);

        // Opcjonalnie: logowanie lub powiadomienie o zakończeniu
        Log::info("Products export completed: {$this->fileName}");
    }

    protected function prepareHeaders($attributes): array
    {
        $headers = [
            'sku',
            'url-key',
            'status',
            'ean',
            'type',
            'parent_sku',
            'uses_stock',
            'stock_qty',
            'in_stock',
            'price',
            'final_price',
            'special_price_from',
            'special_price_to',
            'special_price',
            'price_rule_id',
            'tax_class',
            'categories',
            'thumbnail_images',
            'gallery_images',
            'brand'
        ];

        // Dodanie nagłówków dla wszystkich atrybutów
        foreach ($attributes as $attribute) {
            $headers[] = $attribute->code;
        }

        return $headers;
    }

    protected function prepareProductRow(Product $product, $attributes): array
    {
        $row = [
            $product->sku,
            $product->urlRewrite?->request_path,
            $product->is_active ? 1 : 0,
            $product->ean,
            $product->type,
            $product->parent?->sku,
            $product->uses_stock ? 1 : 0,
            $product->stock_qty,
            $product->in_stock ? 1 : 0,
            $product->price ? $product->price->price : '',
            $product->price ? $product->price->final_price : '',
            $product->price ? $product->price->special_price_from : '',
            $product->price ? $product->price->special_price_to : '',
            $product->price ? $product->price->special_price : '',
            $product->price ? $product->price->price_rule_id : '',
            $product->taxClass ? $product->taxClass->code : '',
            $this->getCategoriesString($product),
            $this->getThumbnailImages($product),
            $this->getGalleryImages($product),
            $product->brand ? $product->brand->code : ''
        ];

        // Dodanie wartości wszystkich atrybutów
        foreach ($attributes as $attribute) {
            $attributeValue = $product->getAttributeTextValue($attribute->code);
            if (is_array($attributeValue)) {
                $attributeValue = implode(config('shopen.export.values_separator'), $attributeValue);
            }
            $row[] = $attributeValue ?? '';
        }

        return $row;
    }

    protected function getThumbnailImages(Product $product)
    {
        $images = [];
        $media = $product->getMedia('default', ['thumbnail' => true]);
        foreach ($media as $mediaItem) {
            $images[] = $mediaItem->getFullUrl();
        }
        return implode(config('shopen.export.values_separator'), $images);
    }

    protected function getGalleryImages(Product $product)
    {
        $images = [];
        $media = $product->getMedia('default', ['gallery' => true]);
        foreach ($media as $mediaItem) {
            $images[] = $mediaItem->getFullUrl();
        }
        return implode(config('shopen.export.values_separator'), $images);
    }

    protected function getCategoriesString(Product $product): string
    {
        if (!$product->categories || $product->categories->isEmpty()) {
            return '';
        }

        return $product->categories->pluck('id')->implode(config('shopen.export.values_separator'));
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }
}