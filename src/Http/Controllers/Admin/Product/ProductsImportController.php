<?php

namespace Shopen\Http\Controllers\Admin\Product;

use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Controllers\Admin\Product\Trait\HasCategoryMap;
use Shopen\Models\Attribute\Attribute;
use Shopen\Models\Category\Category;
use Shopen\Models\Product\Price\ProductPrice;
use Shopen\Models\Product\Product;
use Shopen\Repositories\Category\CategoryAttributeRepository;
use Shopen\Repositories\Product\ProductAttributeRepository;
use Shopen\Services\CsvParser;

class ProductsImportController
{
    use HasCategoryMap;

    public function __construct(
        protected CsvParser $csvParser,
        protected ProductAttributeRepository $productAttributeRepository,
        protected CategoryAttributeRepository $categoryAttributeRepository,
    )
    {}

    public function index(): Response
    {
        return Inertia::render('Admin/Product/Import');
    }


    public function import(): Response
    {
        $this->createCategoryMap();

        $data = $this->importFile();

        return Inertia::render('Admin/Product/Import', $data);
    }

    private function importFile(): array
    {
        $validator = Validator::make(request()->all(), [
            'file' => 'required|file|mimes:csv,txt',
        ]);

        if ($validator->fails()) {
            return [
                'validation_status' => 'error',
                'validation_message' => 'Błąd walidacji pliku'
            ];
        }

        try {
            $file = request()->file('file');
            $csvData = $this->parseCsvFile($file);

            if (empty($csvData)) {
                return [
                    'validation_status' => 'error',
                    'validation_message' => 'Niepoprawny plik'
                ];
            }

            $result = $this->processImport($csvData);

            return [
                'import_summary' => $result
            ];

        } catch (Exception $e) {
            return [
                'validation_status' => 'error',
                'validation_message' => 'Wystąpił błąd przy imporcie: ' . $e->getMessage()
            ];
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

        if ($isUpdate) {
            $this->updateBasicProductData($product, $row);
        } else {
            $this->setBasicProductData($product, $row);
        }
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
            $thumbnailMedia = $product->getMedia('default', ['thumbnail' => true]);
            foreach ($thumbnailMedia as $item) {
               $item->setCustomProperty('thumbnail', false);
               $item->save();
            }
        }
        if (isset($row['gallery_images'])) {
            $galleryMedia = $product->getMedia('default', ['gallery' => true]);
            foreach ($galleryMedia as $item) {
                $item->setCustomProperty('gallery', false);
                $item->save();
            }
        }
        if (isset($row['meta_images'])) {
            $metaMedia = $product->getMedia('default', ['meta' => true]);
            foreach ($metaMedia as $item) {
                $item->setCustomProperty('meta', false);
                $item->save();
            }
        }
        foreach ($product->getMedia() as $mediaItem) {
            if (Collection::make($mediaItem->custom_properties)->filter()->count() === 0) {
                $mediaItem->delete();
            }
        }
        $thumbnails = $this->getImages($row, 'thumbnail_images');
        $galleryImages = $this->getImages($row, 'gallery_images');
        $metaImages = $this->getImages($row, 'meta_images');
        foreach ($thumbnails as $thumbnail) {
            $media[$thumbnail]['thumbnail'] = true;
        }
        foreach ($galleryImages as $image) {
            $media[$image]['gallery'] = true;
        }
        foreach ($metaImages as $image) {
            $media[$image]['meta'] = true;
        }
        foreach ($media as $file => $mediaItem) {
            $product->addMedia($file)->preservingOriginal()->withCustomProperties($mediaItem)->toMediaCollection();
        }
    }

    protected function getImages($row, $type)
    {
        $media = [];
        if (isset($row[$type])) {
            $images = explode(config('shopen.export.values_separator'), $row[$type]);
            foreach ($images as $image) {
                if (!$image) {
                    continue;
                }
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

    private function setBasicProductData(Product $product, array $row): void
    {
        $product->ean = $row['ean'] ?? null;
        $product->type = mb_strtolower($row['type']) === Product::TYPE_CONFIGURABLE ? Product::TYPE_CONFIGURABLE : Product::TYPE_SIMPLE;
        $product->parent_id = !empty($row['parent_id']) ? (int)$row['parent_id'] : null;
        $product->uses_stock = $this->parseBooleanValue($row['uses_stock'] ?? 'no');
        $product->stock_qty = !empty($row['stock_qty']) ? (int)$row['stock_qty'] : 0;
        $product->in_stock = $this->parseBooleanValue($row['in_stock'] ?? 'no');
        $product->is_virtual = $this->parseBooleanValue($row['is_virtual'] ?? 'no');
        $product->is_voucher = $this->parseBooleanValue($row['is_voucher'] ?? 'no');
        $product->is_new = $this->parseBooleanValue($row['is_new'] ?? 'no');
        $product->is_new_to = $this->parseDate($row['is_new_to'] ?? null);
        $product->is_active = $this->parseDate($row['status'] ?? null);
    }

    private function updateBasicProductData(Product $product, array $row): void
    {
        if (isset($row['ean'])) {
            $product->ean = $row['ean'] ?? null;
        }
        if (isset($row['type'])) {
            $product->type = mb_strtolower($row['type']) === Product::TYPE_CONFIGURABLE ? Product::TYPE_CONFIGURABLE : Product::TYPE_SIMPLE;
        }
        if (isset($row['parent_id'])) {
            $product->parent_id = !empty($row['parent_id']) ? (int)$row['parent_id'] : null;
        }
        if (isset($row['uses_stock'])) {
            $product->uses_stock = $this->parseBooleanValue($row['uses_stock'] ?? 'no');
        }
        if (isset($row['stock_qty'])) {
            $product->stock_qty = !empty($row['stock_qty']) ? (int)$row['stock_qty'] : 0;
        }
        if (isset($row['in_stock'])) {
            $product->in_stock = $this->parseBooleanValue($row['in_stock'] ?? 'no');
        }
        if (isset($row['is_virtual'])) {
            $product->is_virtual = $this->parseBooleanValue($row['is_virtual'] ?? 'no');
        }
        if (isset($row['is_voucher'])) {
            $product->is_voucher = $this->parseBooleanValue($row['is_voucher'] ?? 'no');
        }
        if (isset($row['is_new'])) {
            $product->is_new = $this->parseBooleanValue($row['is_new'] ?? 'no');
        }
        if (isset($row['is_new_to'])) {
            $product->is_new_to = $this->parseDate($row['is_new_to'] ?? null);
        }
        if (isset($row['status'])) {
            $product->is_active = $this->parseDate($row['status'] ?? null);
        }
    }

    private function updateProductPrice(Product $product, array $row): void
    {
        if ($product->isConfigurable() || !isset($row['price'])) {
            return;
        }
        $price = ProductPrice::firstOrNew(['product_id' => $product->id]);
        $price->price = !empty($row['price']) ? (float)$row['price'] : null;
        $price->final_price = !empty($row['final_price']) ? (float)$row['final_price'] : null;
        if (!$price->final_price) {
            $price->final_price = $price->price;
        }
        $price->special_price = !empty($row['special_price']) ? (float)$row['special_price'] : null;
        $price->special_price_from = !empty($row['special_price_from']) ? $row['special_price_from'] : null;
        $price->special_price_to = !empty($row['special_price_to']) ? $row['special_price_to'] : null;
        $price->rule_id = !empty($row['price_rule_id']) ? $row['price_rule_id'] : null;
        $price->save();
    }

    private function updateProductCategories(Product $product, array $row): void
    {
        if (!empty($row['categories'])) {
            $ids = [];
            $categories = array_map('trim', explode(config('shopen.export.values_separator'), $row['categories']));
            foreach ($categories as $categoryPath) {
                $categoryPathNames = explode('/', $categoryPath);
                $parentId = null;
                foreach ($categoryPathNames as $i => $categoryName) {
                    $category = $this->getCategory($categoryName, $parentId);
                    if ($category) {
                        $parentId = $category->id;
                        if ($i === count($categoryPathNames) - 1) {
                            $ids[] = $category->id;
                        }
                    } else {
                        break;
                    }
                }
            }

            if (!empty($ids)) {
                $product->categories()->sync($ids);
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

    private function parseBooleanValue($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        $value = strtolower(trim($value));
        return $value === '1';
    }

    private function parseDate($value): ?Carbon
    {
        if (empty($value) || !is_string($value)) {
            return null;
        }

        try {
            return Carbon::parse(trim($value));
        } catch (\Exception $e) {
            return null;
        }
    }


}