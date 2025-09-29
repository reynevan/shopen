<?php

namespace Shopen\Http\Controllers\Admin\Product;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Shopen\Models\Attribute\Attribute;
use Shopen\Models\Category\Category;
use Shopen\Models\Product\Price\ProductPrice;
use Shopen\Models\Product\Product;
use Shopen\Repositories\Product\ProductAttributeRepository;
use Shopen\Services\CsvParser;

readonly class ProductsImportController
{
    public function __construct(
        protected CsvParser $csvParser,
        protected ProductAttributeRepository $productAttributeRepository,
    )
    {

    }

    public function index()
    {

        return Inertia::render('Admin/Product/Import', [

        ]);
    }


    public function import()
    {
        $validator = Validator::make(request()->all(), [
            'file' => 'required|file|mimes:csv,txt',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->with([
                'validation_status' => 'error',
                'validation_message' => 'Błąd walidacji pliku'
            ]);
        }

        try {
            $file = request()->file('file');
            $csvData = $this->parseCsvFile($file);

            if (empty($csvData)) {
                return back()->withErrors($validator)->with([
                    'validation_status' => 'error',
                    'validation_message' => 'Niepoprawny plik'
                ]);
            }

            $result = $this->processImport($csvData);

            return back()->with([
                'success' => 'heh',
                'error' => 'nie heh'
            ]);

        } catch (Exception $e) {
            return back()->withErrors($validator)->with([
                'validation_status' => 'error',
                'validation_message' => 'Wystąpił błąd przy imporcie: ' . $e->getMessage()
            ]);
        }
    }

    private function parseCsvFile($file): array
    {
        $csvData = [];
        $handle = fopen($file->getPathname(), 'r');

        if ($handle === false) {
            throw new Exception('Cannot open CSV file');
        }

        // Odczytanie nagłówków
        $headers = fgetcsv($handle);
        if (!$headers) {
            fclose($handle);
            throw new Exception('Cannot read CSV headers');
        }

        // Odczytanie danych
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) === count($headers)) {
                $csvData[] = array_combine($headers, $row);
            }
        }

        fclose($handle);
        return $csvData;
    }

    private function processImport(array $csvData): array
    {
        $stats = [
            'total_rows' => count($csvData),
            'created' => 0,
            'updated' => 0,
            'errors' => 0,
            'error_messages' => []
        ];

        $attributes = $this->productAttributeRepository->getAll();
        
        foreach ($csvData as $rowIndex => $row) {
            try {
                DB::beginTransaction();
                $this->processProductRow($row, $attributes, $stats);
                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                dd($e);
                $stats['errors']++;
                $stats['error_messages'][] = "Row " . ($rowIndex + 2) . ": " . $e->getMessage();
            }
        }
        foreach ($csvData as $rowIndex => $row) {
            if (empty($row['parent_sku'])) {
                continue;
            }
            try {
            $product = Product::query()->where('sku', $row['sku'])->first();
            $parent = Product::query()->where('sku', $row['parent_sku'])->first();
            $product->parent_id = $parent->id;
            $product->save();
            } catch (Exception $e) {
                $stats['errors']++;
                $stats['error_messages'][] = "Row " . ($rowIndex + 2) . ": " . $e->getMessage();
            }
        }

        return $stats;
    }

    /**
     * @throws Exception
     */
    private function processProductRow(array $row, $attributes, array &$stats): void
    {
        if (empty($row['sku'])) {
            throw new Exception('sku jest wymagane');
        }

        $product = Product::query()->where('sku', $row['sku'])->first();
        $isUpdate = $product !== null;

        if (!$product) {
            $product = new Product();
            $product->sku = $row['sku'];
        }

        $this->updateBasicProductData($product, $row);
        $this->updateProductAttributes($product, $row, $attributes);

        $product->save();
        $product->createUrlRewrite($row['url_key'] ?? $row['sku']);

        $this->updateProductPrice($product, $row);
        $this->updateProductCategories($product, $row);
        $this->updateProductImages($product, $row);

        if ($isUpdate) {
            $stats['updated']++;
        } else {
            $stats['created']++;
        }
    }

    protected function updateProductImages(Product $product, array $row): void
    {
        $media = [];
        if (isset($row['thumbnail_images'])) {
            $mediaIds = $product->getMedia('default', ['thumbnail' => true])->pluck('id')->toArray();
            foreach ($mediaIds as $mediaId) {
                $product->deleteMedia($mediaId);
            }
        }
        if (isset($row['gallery_images'])) {
            $mediaIds = $product->getMedia('default', ['gallery_images' => true])->pluck('id')->toArray();
            foreach ($mediaIds as $mediaId) {
                $product->deleteMedia($mediaId);
            }
        }
        $thumbnails = $this->getImages($row, 'thumbnail_images');
        $galleryImage = $this->getImages($row, 'gallery_images');
        foreach ($thumbnails as $thumbnail) {
            $media[$thumbnail]['thumbnail'] = true;
        }
        foreach ($galleryImage as $image) {
            $media[$image]['gallery'] = true;
        }
        foreach ($media as $file => $mediaItem) {
            $product->addMedia($file)->withCustomProperties($mediaItem)->toMediaCollection();
        }
    }

    protected function getImages($row, $type)
    {
        $media = [];
        if (isset($row[$type])) {
            $images = explode(config('shopen.export.values_separator'), $row[$type]);
            foreach ($images as $image) {
                if (str_starts_with($image, 'http')) {
                    continue;
                }
                $filePath = storage_path('import/images/' . $image);
                if (!File::exists($filePath)) {
                    continue;
                }
                $media[] = $filePath;
            }
        }
        return $media;
    }

    private function updateBasicProductData(Product $product, array $row): void
    {
        $product->ean = $row['ean'] ?? null;
        $product->type = mb_strtolower($row['type']) === Product::TYPE_CONFIGURABLE ? Product::TYPE_CONFIGURABLE : Product::TYPE_SIMPLE;
        $product->parent_id = !empty($row['parent_id']) ? (int)$row['parent_id'] : null;
        $product->uses_stock = $this->parseBooleanValue($row['uses_stock'] ?? 'no');
        $product->stock_qty = !empty($row['stock_qty']) ? (int)$row['stock_qty'] : 0;
        $product->in_stock = $this->parseBooleanValue($row['in_stock'] ?? 'no');
    }

    private function updateProductPrice(Product $product, array $row): void
    {
        if ($product->isConfigurable()) {
            return;
        }
        $price = ProductPrice::firstOrNew(['product_id' => $product->id]);
        $price->price = !empty($row['price']) ? (float)$row['price'] : null;
        $price->final_price = !empty($row['final_price']) ? (float)$row['final_price'] : null;
        $price->special_price = !empty($row['special_price']) ? (float)$row['special_price'] : null;
        $price->special_price_from = !empty($row['special_price_from']) ? $row['special_price_from'] : null;
        $price->special_price_to = !empty($row['special_price_to']) ? $row['special_price_to'] : null;
        $price->rule_id = !empty($row['price_rule_id']) ? $row['price_rule_id'] : null;
        $price->save();
    }

    private function updateProductCategories(Product $product, array $row): void
    {
        if (!empty($row['categories'])) {
            $categoryIds = array_map('trim', explode(config('shopen.export.values_separator'), $row['categories']));
            $categoryIds = array_filter($categoryIds, 'is_numeric');

            $existingCategories = Category::whereIn('id', $categoryIds)->pluck('id')->toArray();

            if (!empty($existingCategories)) {
                $product->categories()->sync($existingCategories);
            }
        }
    }

    private function updateProductAttributes(Product $product, array $row, $attributes): void
    {
        foreach ($attributes as $attribute) {
            if (isset($row[$attribute->code])) {
                $value = $row[$attribute->code];
                if ($attribute->isMultiselect()) {
                    $value = explode(config('shopen.export.values_separator'), $value);
                }
                $product->setCustomAttribute($attribute->code, $value);
            }
        }
        $product->save();
    }

    private function convertAttributeValue($value, string $backendType)
    {
        switch ($backendType) {
            case 'bool':
                return $this->parseBooleanValue($value) ? 1 : 0;
            case 'int':
                return (int)$value;
            case 'decimal':
                return (float)$value;
            case 'date':
                return date('Y-m-d', strtotime($value));
            case 'string':
            case 'text':
            default:
                return $value;
        }
    }

    private function parseBooleanValue($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        $value = strtolower(trim($value));
        return $value === '1';
    }


}