<?php

namespace Shopen\Services\Import\Magento;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Shopen\Models\Category\Category;
use Shopen\Models\Product\Product;
use Throwable;

trait ProductsImporter
{
    public function importProducts()
    {
        $productEntityTypeId = DB::connection($this->connectionName)
            ->table('eav_entity_type')
            ->where('entity_type_code', 'catalog_product')
            ->value('entity_type_id');

        if (!$productEntityTypeId) {
            return;
        }

        // Interesujące nas kody atrybutów
        $attributeCodes = [
            'name',
            'description',
            'short_description',
            'status',
            'sku',
            'price',
            'special_price',
            'special_from_date',
            'special_to_date',
            'tax_class_id',
            'weight',
            'visibility',
            'url_key',
            // atrybuty custom
            'ceneo_category_id',
            'manufacturer',
            'color',
            'material',
            'color2',
            'size',
            'materials',
            'meta_description',
            'meta_title'
        ];

        // Pobierz definicje atrybutów (id + backend_type)
        $attributeDefs = DB::connection($this->connectionName)
            ->table('eav_attribute')
            ->where('entity_type_id', $productEntityTypeId)
            ->whereIn('attribute_code', $attributeCodes)
            ->select('attribute_id', 'attribute_code', 'backend_type', 'frontend_input')
            ->get()
            ->mapWithKeys(function ($row) {
                return [$row->attribute_code => [
                    'id' => (int)$row->attribute_id,
                    'type' => $row->backend_type,
                    'frontend_input' => $row->frontend_input,
                ]];
            })
            ->toArray();

        $optionableCodes = [
            'manufacturer', 'color', 'material', 'color2', 'size', 'materials',
            // visibility i tax_class_id też bywają selectami, ale użytkownik prosił o etykiety dla 'attributes'
        ];
        $optionLabels = $this->fetchAttributeOptionLabels($attributeDefs, $optionableCodes, 1);

        // Mapy: attribute_code => attribute_id lub null, jeśli brak w instancji
        $attr = [];
        foreach ($attributeCodes as $code) {
            $attr[$code] = $attributeDefs[$code]['id'] ?? null;
        }

        // Bazowa lista produktów
        $products = DB::connection($this->connectionName)
            ->table('catalog_product_entity')
            ->select(['entity_id', 'type_id', 'sku', 'created_at', 'updated_at'])
            ->orderBy('entity_id')
            ->get()
            ->map(fn($r) => (array)$r)
            ->toArray();

        if (empty($products)) {
            return;
        }

        $productIds = array_column($products, 'entity_id');

        $values0 = $this->fetchProductAttributeValuesForStore($productIds, $attributeDefs, 0);

        $values1 = $this->fetchProductAttributeValuesForStore($productIds, $attributeDefs, 1);

        $productCategories = $this->fetchProductCategories($productIds);

        $stockData = $this->fetchStockData($productIds);

        $mediaGallery = $this->fetchMediaGallery($productIds);

        $configurableLinks = $this->fetchConfigurableLinks($productIds);

        // Zbuduj finalny array
        $result = [];
        foreach ($products as $p) {
            $id = (int)$p['entity_id'];
            $sku = $p['sku'];

            $get = function (string $code) use ($values0, $values1, $id) {
                return $values1[$code][$id] ?? $values0[$code][$id] ?? null;
            };

            $status = (int)($get('status') ?? 0);
            $isActive = $status === 1;

            if (!$isActive) {
                continue;
            }

            // Waga i has_weight – w niektórych instancjach has_weight nie występuje
            $weight = $get('weight');

            // Widoczność (1: Not visible individually, 2: Catalog, 3: Search, 4: Catalog, Search)
            $visibility = $get('visibility');

            // Ceny
            $price = $get('price');
            $specialPrice = $get('special_price');
            $specialFrom = $get('special_from_date');
            $specialTo = $get('special_to_date');

            // Stock
            $stockQty = $stockData[$id]['qty'] ?? null;
            $stockStatus = $stockData[$id]['is_in_stock'] ?? null;

            // Media
            $media = $mediaGallery[$id] ?? [];

            // Configurations (jeśli produkt jest configurable – lista ID powiązanych simple)
            $configurations = $configurableLinks[$id] ?? [];

            $mapOption = function (?string $code, $raw) use ($optionLabels) {
                if ($code === null || !isset($optionLabels[$code]) || $raw === null || $raw === '') {
                    return $raw;
                }
                $labelsMap = $optionLabels[$code];

                // multiselect ma zwykle wartości rozdzielone przecinkami
                if (is_string($raw) && str_contains($raw, ',')) {
                    $ids = array_filter(array_map('trim', explode(',', $raw)), fn($v) => $v !== '');
                    $labels = [];
                    foreach ($ids as $id) {
                        $labels[] = $labelsMap[(int)$id] ?? $id;
                    }
                    return $labels; // tablica etykiet dla multiselect
                }

                // select (pojedynczy)
                $intId = is_numeric($raw) ? (int)$raw : $raw;
                return $labelsMap[$intId] ?? $raw;
            };

            // Atrybuty custom
            $customAttrs = [
                'ceneo_category_id' => $mapOption('ceneo_category_id', $get('ceneo_category_id')),
                'manufacturer' => $mapOption('manufacturer', $get('manufacturer')),
                'color' => $mapOption('color', $get('color')),
                'material' => $mapOption('material', $get('material')),
                'size' => $mapOption('size', $get('size')),
                'meta_description' => $mapOption('meta_description', $get('meta_description')),
                'meta_title' => $mapOption('meta_title', $get('meta_title')),
            ];
            $categories = $productCategories[$id] ?? [];
            $result[] = [
                'id' => $id,
                'name' => $get('name'),
                'description' => $get('description'),
                'short_description' => $get('short_description'),
                'is_active' => (bool)$isActive,
                'sku' => $sku,
                'prices' => [
                    'price' => $price,
                    'special_price' => $specialPrice,
                    'special_price_from' => $specialFrom,
                    'special_price_to' => $specialTo,
                ],
                'stock_qty' => $stockQty !== null ? (float)$stockQty : null,
                'stock_status' => $stockStatus !== null ? (int)$stockStatus : null,
                'weight' => $weight !== null ? (float)$weight : null,
                'visibility' => $visibility !== null ? (int)$visibility : null,
                'configurations' => $configurations,
                'media' => $media,
                'url_key' => $get('url_key'),
                'attributes' => $customAttrs,
                'categories' => $categories
            ];
        }
        $count = count($result);
        foreach ($result as $i => $data) {
            echo "$i/$count\r";
            try {
                $this->importProduct($data);
            } catch (\Exception $e) {
                echo "\n" . $e->getMessage() . "\n";
            }
        }
    }

    protected function importProduct($data)
    {
        $product = Product::query()->where('sku', $data['sku'])->firstOrNew(['sku' => $data['sku']]);
        $product->type = Product::TYPE_SIMPLE;
        $product->visible_individually = $data['visibility'] > 1;
        $product->tax_class_id = 1;
        $product->stock_qty = $data['stock_qty'];
        $product->in_stock = $data['stock_status'];
        $product->uses_stock = true;
        $product->setCustomAttribute('is_active', $data['is_active']);
        $product->setCustomAttribute('name', $data['name']);
        $product->setCustomAttribute('description', $data['description']);
        $product->setCustomAttribute('short_description', $data['short_description']);
        foreach ($data['attributes'] as $code => $value) {
            if ($value && !in_array($code, ['ceneo_category_id', 'manufacturer'])) {
                $product->setCustomAttribute($code, $value);
            }
        }
        $product->save();
        $product->setPrice($data['prices']);

        $product->createOrUpdateSeoForStore(1, [
            'seo_title' => $data['meta_title'] ?? $data['name'],
            'seo_description' => $data['meta_description'] ?? $data['short_description'] ?? null,
        ]);

        $categoryIds = [];
        foreach ($data['categories'] as $catData) {
            if (in_array($catData['name'], ['Default Category', 'Root Catalog'])) {
                continue;
            }
            $categories = Category::query()
                ->tap(function(Builder $query) use ($catData) {
                    if (!in_array($catData['parent_name'], ['Default Category', 'Root Catalog'])) {
                        $query->whereHas('parent', function ($query) use ($catData) {
                            $query
                                ->filterByAttribute('is_active', true)
                                ->filterByAttribute('name', $catData['parent_name']);
                        });
                    }
                })
                ->filterByAttribute('name', $catData['name'])
                ->filterByAttribute('is_active', true)
                ->get();
            if ($categories->isEmpty()) {
                continue;
            }
            $categoryIds[] = $categories->first()->id;
        }

        $product->categories()->sync($categoryIds);
        $product->createUrlRewrite($data['url_key']);

        if ($product->getMedia()->count() === 0) {
            foreach ($data['media'] as $index => $media) {
                try {
                    $product
                        ->addMediaFromUrl(trim($this->url, '/') . '/pub/media/catalog/product' . $media['file'])
                        ->preservingOriginal()
                        ->setOrder($index)
                        ->withCustomProperties(['gallery' => true, 'thumbnail' => $index < 2])
                        ->toMediaCollection();
                } catch (Throwable $e) {}
            }
        }

    }

    private function fetchAttributeOptionLabels(array $attributeDefs, array $attributeCodes, int $storeId = 1): array
    {
        // Wyfiltruj tylko atrybuty, które istnieją i mają source z opcjami (typowo frontend_input in ['select','multiselect'])
        $attrIds = [];
        foreach ($attributeCodes as $code) {
            if (isset($attributeDefs[$code]['id'])) {
                $attrIds[] = (int)$attributeDefs[$code]['id'];
            }
        }
        if (empty($attrIds) || !$this->tableExists('eav_attribute_option')) {
            return [];
        }

        // Pobranie opcji i etykiet
        $conn = DB::connection($this->connectionName);

        $options = $conn->table('eav_attribute_option as o')
            ->whereIn('o.attribute_id', $attrIds)
            ->select(['o.attribute_id', 'o.option_id'])
            ->get();

        if ($options->isEmpty() || !$this->tableExists('eav_attribute_option_value')) {
            return [];
        }

        // Pobierz etykiety w store_id preferencyjnie (jeśli brak, fallback do store_id=0)
        $optionIds = $options->pluck('option_id')->all();

        $labelsPreferred = $conn->table('eav_attribute_option_value')
            ->whereIn('option_id', $optionIds)
            ->where('store_id', $storeId)
            ->select(['option_id', 'value'])
            ->get()
            ->pluck('value', 'option_id')
            ->toArray();

        $labelsDefault = $conn->table('eav_attribute_option_value')
            ->whereIn('option_id', $optionIds)
            ->where('store_id', 0)
            ->select(['option_id', 'value'])
            ->get()
            ->pluck('value', 'option_id')
            ->toArray();

        // Mapa attribute_id => code
        $attrIdToCode = [];
        foreach ($attributeDefs as $code => $def) {
            if (isset($def['id'])) {
                $attrIdToCode[(int)$def['id']] = $code;
            }
        }

        $out = [];
        foreach ($options as $opt) {
            $code = $attrIdToCode[(int)$opt->attribute_id] ?? null;
            if (!$code) continue;
            if (!isset($out[$code])) $out[$code] = [];
            $label = $labelsPreferred[$opt->option_id] ?? ($labelsDefault[$opt->option_id] ?? null);
            if ($label !== null) {
                $out[$code][(int)$opt->option_id] = $label;
            }
        }

        return $out;
    }

    /**
     * Pobiera wartości atrybutów produktów dla podanego store_id = 0
     * w oparciu o backend_type i attribute_id z eav_attribute.
     * Zwraca mapę: [attribute_code => [product_id => value]]
     */
    private function fetchProductAttributeValuesForStore(array $productIds, array $attributeDefs, int $storeId = 1): array
    {
        if (empty($productIds)) {
            return [];
        }

        // Grupuj atrybuty po backend_type
        $byType = [];
        foreach ($attributeDefs as $code => $def) {
            $byType[$def['type']][] = ['code' => $code, 'id' => $def['id']];
        }

        $out = [];
        foreach ($byType as $type => $items) {
            if (!$type || $type === 'static') {
                // 'static' znajdują się w catalog_product_entity – pominąć tutaj
                continue;
            }

            $table = "catalog_product_entity_{$type}";
            $attrIds = array_column($items, 'id');

            $rows = DB::connection($this->connectionName)
                ->table($table)
                ->whereIn('entity_id', $productIds)
                ->where('store_id', $storeId)
                ->whereIn('attribute_id', $attrIds)
                ->select(['entity_id', 'attribute_id', 'value'])
                ->get();

            // Mapa attribute_id -> code
            $idToCode = [];
            foreach ($items as $it) {
                $idToCode[$it['id']] = $it['code'];
            }

            foreach ($rows as $r) {
                $code = $idToCode[$r->attribute_id] ?? null;
                if ($code === null) continue;
                if (!isset($out[$code])) $out[$code] = [];
                $out[$code][(int)$r->entity_id] = $r->value;
            }
        }

        // Uzupełnij 'sku' (static w cpe)
        $skuById = DB::connection($this->connectionName)
            ->table('catalog_product_entity')
            ->whereIn('entity_id', $productIds)
            ->pluck('sku', 'entity_id')
            ->toArray();

        $out['sku'] = array_map(fn($v) => $v, $skuById);

        return $out;
    }

    /**
     * Pobiera dane stockowe. Obsługuje MSI (magento 2.3+) oraz legacy cataloginventory_stock_item.
     * Zwraca mapę: [product_id => ['qty' => float|null, 'is_in_stock' => int|null]]
     */
    private function fetchStockData(array $productIds): array
    {
        if (empty($productIds)) {
            return [];
        }

        $conn = DB::connection($this->connectionName);

        // Spróbuj MSI: inventory_source_item + catalog_product_entity (sku)
        $hasMsi = $this->tableExists('inventory_source_item');
        $stock = [];

        if ($hasMsi) {
            // SKU -> ID
            $skuById = $conn->table('catalog_product_entity')
                ->whereIn('entity_id', $productIds)
                ->pluck('sku', 'entity_id')
                ->toArray();

            if (!empty($skuById)) {
                $rows = $conn->table('inventory_source_item')
                    ->whereIn('sku', array_values($skuById))
                    ->select(['sku', 'quantity', 'status'])
                    ->get();

                // Agreguj po SKU – jeżeli wiele źródeł, zsumuj qty, status 1 jeśli jakiekolwiek źródło ma status=1
                $agg = [];
                foreach ($rows as $r) {
                    $s = $r->sku;
                    if (!isset($agg[$s])) {
                        $agg[$s] = ['qty' => 0.0, 'is_in_stock' => 0];
                    }
                    $agg[$s]['qty'] += (float)$r->quantity;
                    $agg[$s]['is_in_stock'] = $agg[$s]['is_in_stock'] || ((int)$r->status === 1) ? 1 : 0;
                }

                foreach ($skuById as $pid => $sku) {
                    if (isset($agg[$sku])) {
                        $stock[$pid] = $agg[$sku];
                    }
                }
            }
        }

        // Legacy fallback: cataloginventory_stock_item
        if (empty($stock) && $this->tableExists('cataloginventory_stock_item')) {
            $rows = $conn->table('cataloginventory_stock_item')
                ->whereIn('product_id', $productIds)
                ->select(['product_id', 'qty', 'is_in_stock'])
                ->get();

            foreach ($rows as $r) {
                $stock[(int)$r->product_id] = [
                    'qty' => $r->qty !== null ? (float)$r->qty : null,
                    'is_in_stock' => $r->is_in_stock !== null ? (int)$r->is_in_stock : null,
                ];
            }
        }

        return $stock;
    }

    /**
     * Pobiera media gallery (obrazki) dla produktów.
     * Zwraca mapę: [product_id => [ ['value_id'=>..., 'file'=>..., 'label'=>..., 'position'=>..., 'disabled'=>...], ... ]]
     */
    private function fetchMediaGallery(array $productIds): array
    {
        $out = [];
        if (empty($productIds) || !$this->tableExists('catalog_product_entity_media_gallery')) {
            return $out;
        }

        $conn = DB::connection($this->connectionName);

        // Związek cpe.entity_id -> media_gallery via catalog_product_entity_media_gallery_value
        $rows = $conn->table('catalog_product_entity_media_gallery as mg')
            ->join('catalog_product_entity_media_gallery_value as mgv', 'mg.value_id', '=', 'mgv.value_id')
            ->whereIn('mgv.entity_id', $productIds)
            ->select([
                'mgv.entity_id as product_id',
                'mg.value_id',
                'mg.value as file',
                'mgv.label',
                'mgv.position',
                'mgv.disabled',
            ])
            ->orderBy('mgv.entity_id')
            ->orderBy('mgv.position')
            ->get();

        foreach ($rows as $r) {
            $pid = (int)$r->product_id;
            if (!isset($out[$pid])) $out[$pid] = [];
            $out[$pid][] = [
                'value_id' => (int)$r->value_id,
                'file' => $r->file,
                'label' => $r->label,
                'position' => $r->position !== null ? (int)$r->position : null,
                'disabled' => $r->disabled !== null ? (int)$r->disabled : null,
            ];
        }

        return $out;
    }

    /**
     * Pobiera powiązania configurable -> simple.
     * Zwraca mapę: [configurable_product_id => [simple_id1, simple_id2, ...]]
     */
    private function fetchConfigurableLinks(array $productIds): array
    {
        $out = [];
        if (!$this->tableExists('catalog_product_super_link')) {
            return $out;
        }

        $conn = DB::connection($this->connectionName);

        // Ogranicz do konfigurowalnych w przekazanej puli productIds
        $rows = $conn->table('catalog_product_super_link as l')
            ->join('catalog_product_entity as parent', 'l.parent_id', '=', 'parent.entity_id')
            ->join('catalog_product_entity as child', 'l.product_id', '=', 'child.entity_id')
            ->whereIn('l.parent_id', $productIds)
            ->select(['l.parent_id as configurable_id', 'l.product_id as simple_id'])
            ->get();

        foreach ($rows as $r) {
            $pid = (int)$r->configurable_id;
            if (!isset($out[$pid])) $out[$pid] = [];
            $out[$pid][] = (int)$r->simple_id;
        }

        return $out;
    }

    /**
     * Zwraca mapę: [product_id => [ ['id'=>category_id, 'name'=>name, 'parent_id'=>parent_id], ... ]]
     */
    private function fetchProductCategories(array $productIds, int $storeId = 0): array
    {
        if (empty($productIds) || !$this->tableExists('catalog_category_product')) {
            return [];
        }

        $conn = DB::connection($this->connectionName);

        // Powiązania produkt-kategoria
        $links = $conn->table('catalog_category_product')
            ->whereIn('product_id', $productIds)
            ->select(['product_id', 'category_id'])
            ->get();

        if ($links->isEmpty()) {
            return [];
        }

        $categoryIds = array_values(array_unique($links->pluck('category_id')->all()));

        // Pobierz parent_id z catalog_category_entity
        $catsBase = $conn->table('catalog_category_entity')
            ->whereIn('entity_id', $categoryIds)
            ->select(['entity_id', 'parent_id'])
            ->get()
            ->keyBy('entity_id')
            ->toArray();

        // Ustal attribute_id i backend_type dla name
        $categoryEntityTypeId = $conn->table('eav_entity_type')
            ->where('entity_type_code', 'catalog_category')
            ->value('entity_type_id');

        $nameAttr = $conn->table('eav_attribute')
            ->where('entity_type_id', $categoryEntityTypeId)
            ->where('attribute_code', 'name')
            ->select(['attribute_id', 'backend_type'])
            ->first();

        $namesById = [];
        if ($nameAttr) {
            $nameTable = "catalog_category_entity_{$nameAttr->backend_type}";
            $namesById = $conn->table($nameTable)
                ->where('attribute_id', $nameAttr->attribute_id)
                ->where('store_id', $storeId)
                ->pluck('value', 'entity_id')
                ->toArray();

            // Fallback do store_id=0 dla brakujących
            if (count($namesById) < count($categoryIds)) {
                $missing = array_diff($categoryIds, array_keys($namesById));
                if (!empty($missing)) {
                    $fallback = $conn->table($nameTable)
                        ->where('attribute_id', $nameAttr->attribute_id)
                        ->where('store_id', 0)
                        ->whereIn('entity_id', $missing)
                        ->pluck('value', 'entity_id')
                        ->toArray();
                    $namesById = $namesById + $fallback;
                }
            }
        }

        // Zbuduj mapę produktów
        $out = [];
        foreach ($links as $l) {
            $pid = (int)$l->product_id;
            $cid = (int)$l->category_id;
            $parentId = isset($catsBase[$cid]) ? (int)$catsBase[$cid]->parent_id : null;
            $name = $namesById[$cid] ?? null;

            $out[$pid] ??= [];
            $out[$pid][] = [
                'id' => $cid,
                'name' => $name,
                'parent_id' => $parentId,
                'parent_name' => $namesById[$parentId] ?? null,
            ];
        }

        return $out;
    }
}