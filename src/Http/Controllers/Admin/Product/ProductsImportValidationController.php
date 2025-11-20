<?php

namespace Shopen\Http\Controllers\Admin\Product;

use DateTime;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Shopen\Http\Controllers\Admin\Product\Trait\HasCategoryMap;
use Shopen\Models\Attribute\Attribute;
use Shopen\Models\Category\Category;
use Shopen\Models\Product\Product;
use Shopen\Models\UrlRewrite;
use Shopen\Repositories\Category\CategoryAttributeRepository;
use Shopen\Repositories\Product\ProductAttributeRepository;
use Shopen\Services\CsvParser;

class ProductsImportValidationController
{
    use HasCategoryMap;

    public function __construct(
        protected CsvParser $csvParser,
        protected ProductAttributeRepository $productAttributeRepository,
        protected CategoryAttributeRepository $categoryAttributeRepository,
    ) {}

    public function validate()
    {
        $this->createCategoryMap();
        $data = $this->validateFile();

        return Inertia::render('Admin/Product/Import', $data);
    }

    private function validateFile()
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
            $csvData = $this->csvParser->parseCsvFile($file);

            if (empty($csvData)) {
                return [
                        'validation_status' => 'error',
                        'validation_message' => 'Plik CSV jest pusty lub nieprawidłowy'
                    ];
            }

            $validationResult = $this->validateCsvData($csvData);

            return [
                'validation_status' => $validationResult['is_valid'] ? 'success' : 'error',
                'validation_message' => $validationResult['is_valid']
                    ? 'Walidacja CSV przebiegła pomyślnie'
                    : 'Walidacja CSV nie powiodła się',
                'validation_summary' => [
                    'total_rows' => $validationResult['total_rows'],
                    'valid_rows' => $validationResult['valid_rows'],
                    'invalid_rows' => $validationResult['invalid_rows'],
                    'missing_headers' => $validationResult['missing_headers'],
                    'duplicate_skus' => $validationResult['duplicate_skus']
                ],
                'validation_errors' => $validationResult['errors'],
                'validation_warnings' => $validationResult['warnings'],
                'is_validated' => true
            ];

        } catch (\Exception $e) {
            return [
                    'validation_status' => 'error',
                    'validation_message' => 'Błąd walidacji: ' . $e->getMessage()
                ];
        }
    }

    private function validateCsvData(array $csvData): array
    {
        $result = [
            'is_valid' => true,
            'total_rows' => count($csvData),
            'valid_rows' => 0,
            'invalid_rows' => 0,
            'missing_headers' => [],
            'duplicate_skus' => [],
            'errors' => [],
            'warnings' => []
        ];

        if (empty($csvData)) {
            $result['is_valid'] = false;
            $result['errors'][] = 'Plik CSV nie zawiera żadnych danych';
            return $result;
        }

        // Sprawdzenie wymaganych nagłówków
        $requiredHeaders = ['sku'];
        $availableHeaders = array_keys($csvData[0]);

        foreach ($requiredHeaders as $header) {
            if (!in_array($header, $availableHeaders)) {
                $result['missing_headers'][] = $header;
                $result['is_valid'] = false;
            }
        }

        // Jeśli brakuje wymaganych nagłówków, nie kontynuuj walidacji
        if (!empty($result['missing_headers'])) {
            return $result;
        }

        // Sprawdzenie duplikatów sku w pliku
        $skus = [];
        $urlKeys = [];
        foreach ($csvData as $i => $row) {
            if (!empty($row['sku'])) {
                if (in_array($row['sku'], $skus)) {
                    $result['duplicate_skus'][] = $row['sku'];
                    $result['is_valid'] = false;
                } else {
                    $skus[] = $row['sku'];
                }
            }
            if (!empty($row['url_key'])) {
                if (in_array($row['url_key'], $urlKeys)) {
                    $result['is_valid'] = false;
                    $rowNumber = $i+2;
                    $result['errors'][] = "Wiersz $rowNumber: Powtórzone url_key: " . $row['url_key'];
                } else {
                    $urlKeys[] = $row['url_key'];
                }
            }
        }
        foreach ($csvData as $i => $row) {
            if (empty($row['parent_sku'])) {
                continue;
            }
            if (in_array($row['parent_sku'], $skus)) {
                continue;
            }
            if (Product::query()->where('sku', $row['parent_sku'])->where('type', Product::TYPE_CONFIGURABLE)->exists()) {
                continue;
            }
            $rowNumber = $i + 2;
            $result['errors'][] = "Wiersz $rowNumber: nieprawidłowe parent_sku: " . $row['parent_sku'];
        }

        $attributes = $this->productAttributeRepository->getAll();

        foreach ($csvData as $rowIndex => $row) {
            $rowNumber = $rowIndex + 2; // +2 bo liczymy od 1 i pomijamy nagłówek
            $rowErrors = $this->validateRow($row, $rowNumber, $attributes, $skus);

            if (!empty($rowErrors)) {
                $result['invalid_rows']++;
                $result['errors'] = array_merge($result['errors'], $rowErrors);
                $result['is_valid'] = false;
            } else {
                $result['valid_rows']++;
            }
        }

        return $result;
    }

    private function validateRow(array $row, int $rowNumber, Collection $attributes, array $skus): array
    {
        if (count(array_unique(array_values($row))) === 1 && array_values($row)[0] === '') {
            return [];
        }
        $existingProduct = $row['sku'] ? Product::query()->where('sku', $row['sku'])->exists() : false;
        $errors = [];
        // Walidacja sku
        if (empty($row['sku'])) {
            $errors[] = "sku jest wymagane";
        } elseif (strlen($row['sku']) > 255) {
            $errors[] = "sku jest za długie (max 255 znaków)";
        }

        // Walidacja name (atrybut)
        $nameValue = $row['name'] ?? '';
        if (empty($nameValue) && !$existingProduct) {
            $errors[] = "Nazwa produktu jest wymagana";
        } elseif (!empty($nameValue) && strlen($nameValue) > 255) {
            $errors[] = "Nazwa produktu jest za długa (max 255 znaków)";
        }

        if (!isset($row['status']) && !$existingProduct) {
            $errors[] = "Status produktu jest wymagany";
        } elseif (isset($row['status'])) {
            $this->validateBool($row, 'status', $errors);
        }

        // Walidacja opcjonalnych pól
        if (!empty($row['ean']) && strlen($row['ean']) > 255) {
            $errors[] = "EAN jest za długi (max 255 znaków)";
        }

        if (!empty($row['type']) && !in_array($row['type'], ['simple', 'configurable'])) {
            $errors[] = "Nieprawidłowy typ produktu '{$row['type']}'";
        }

        if (!empty($row['parent_sku']) && !in_array($row['parent_sku'], $skus) && !Product::query()->where('sku', $row['parent_sku'])->exists()) {
            $errors[] = "parent sku - produkt z takim sku nie istnieje";
        }

        if (!empty($row['stock_qty']) && !is_numeric($row['stock_qty'])) {
            $errors[] = "stock_qty musi być liczbą";
        }

        $this->validateNumeric($row, 'price', $errors);
        $this->validateNumeric($row, 'final_price', $errors);
        $this->validateNumeric($row, 'special_price', $errors);
        $this->validateDate($row, 'special_price_from', $errors);
        $this->validateDate($row, 'special_price_to', $errors);
        $this->validateUrlKey($row, $errors);

        // Walidacja kategorii
        if (!empty($row['categories'])) {
            $categories = array_map('trim', explode(config('shopen.export.values_separator'), $row['categories']));
            foreach ($categories as $categoryPath) {
                $categoryPathNames = explode('/', $categoryPath);
                $parentId = null;
                foreach ($categoryPathNames as $categoryName) {
                    $category = $this->getCategory($categoryName, $parentId);
                    if ($category) {
                        $parentId = $category->parent_id ?? null;
                    } else {
                        $errors[] = 'Nieprawidłowa kategoria: ' . $categoryPath;
                        break;
                    }
                }
            }
        }

        foreach ($attributes as $attribute) {
            if (empty($row[$attribute->code])) {
                continue;
            }
            $this->validateAttribute($row, $attribute, $errors);
        }

        foreach ($errors as $key => $error) {
            $errors[$key] = "Wiersz $rowNumber: $error";
        }
        return $errors;
    }
    protected function validateUrlKey($row, &$errors)
    {
        if (!isset($row['url_key'])) {
            return;
        }
        $rewrite = UrlRewrite::query()->where('request_path', $row['url_key'])->first();
        if (!$rewrite) {
            return;
        }
        if ($rewrite->entity_type !== Product::ENTITY_TYPE) {
            $errors[] = 'url_key jest już używany';
            return;
        }
        if ($rewrite->entity?->sku !== $row['sku']) {
            $errors[] = 'url_key jest już używany';
        }
    }

    protected function validateAttribute($row, Attribute $attribute, &$errors)
    {
        if (!isset($row[$attribute->code])) {
            return;
        }
        if ($attribute->isSelectable()) {
            $this->validateSelectable($row, $attribute, $errors);
            return;
        }
        if ($attribute->backend_type === 'bool') {
            $this->validateBool($row, $attribute->code, $errors);
            return;
        }
        if ($attribute->backend_type === 'date') {
            $this->validateDate($row, $attribute->code, $errors);
            return;
        }
        if ($attribute->backend_type === 'int' || $attribute->backend_type === 'decimal') {
            $this->validateNumeric($row, $attribute->code, $errors);
            return;
        }
    }

    protected function validateSelectable($row, Attribute $attribute, &$errors)
    {
        $options = $attribute->options->pluck('value')->toArray();
        $values = explode(config('shopen.export.values_separator'), $row[$attribute->code]);
        foreach ($values as $value) {
            if (!in_array($value, $options)) {
                $errors[] = "Nieprawidłowa wartość atrybutu {$attribute->code}: " . $value;
            }
        }
    }

    protected function validateNumeric($row, $attr, &$errors)
    {
        if (!empty($row[$attr]) && !is_numeric($row[$attr])) {
            $errors[] = "{$attr} musi być liczbą";
        }
    }

    protected function validateBool($row, $attr, &$errors)
    {
        if (!empty($row[$attr]) && !in_array(strtolower($row[$attr]), ['1', '0'])) {
            $errors[] = "{$attr} - nieprawidłowa wartość";
        }
    }

    protected function validateDate($row, $attr, &$errors)
    {
        if (empty($row[$attr])) {
            return;
        }
        $value = $row[$attr];

        if (!is_string($value)) {
            $errors[] = "{$attr} - nieprawidłowy format";
            return;
        }

        if ($value === '') {
            return;
        }

        $date = Carbon::parse($value);

        if ($date === false || $date->format('Y-m-d') !== $value) {
            $errors[] = "{$attr} - nieprawidłowy format";
        }
    }
}