<?php

namespace Shopen\Console\Commands;

use Illuminate\Console\Command;
use RuntimeException;
use Shopen\Models\Attribute\Attribute;
use Shopen\Models\Ceneo\CeneoCategory;
use Shopen\Models\Product\TaxClass;
use Shopen\Models\Store;
use Shopen\Models\User;

class ShopenInstall extends Command
{
    protected $signature = 'shopen:install {--no-admin} {--fresh}';
    protected $description = 'Installs Shopen';

    public function handle()
    {
        $this->installFrontendDependencies();
        $this->runCommand('migrate' . ($this->option('fresh') ? ':fresh' : ''), [], $this->output);
        $this->createStore();
        $this->importCeneoCategories();
        $this->createAttributes();
        $this->createTaxClass();
        if (!$this->option('no-admin')) {
            $admin = User::query()->where('role', User::ROLE_ADMIN)->first();
            if (!$admin) {
                $this->runCommand('shopen:create-admin-user', [], $this->output);
            }
        }
    }

    protected function installFrontendDependencies()
    {
        $this->info('Adding Shopen frontend dependencies to package.json...');

        $packageJsonPath = base_path('package.json');
        $shopenPackageJsonPath = __DIR__ . '/../../../package.json';

        if (!file_exists($packageJsonPath)) {
            $this->error('package.json not found in your project root.');
            return 1;
        }
        if (!file_exists($shopenPackageJsonPath)) {
            $this->error('package.json not found in the shopen/core package.');
            return 1;
        }

        $rootPackage = json_decode(file_get_contents($packageJsonPath), true);
        $shopenPackage = json_decode(file_get_contents($shopenPackageJsonPath), true);

        $peerDependencies = $shopenPackage['peerDependencies'] ?? [];
        if (empty($peerDependencies)) {
            $this->info('No peer dependencies to install.');
            return 0;
        }

        $devDependencies = &$rootPackage['devDependencies'];
        $updated = false;

        foreach ($peerDependencies as $package => $version) {
            if (!isset($devDependencies[$package])) {
                $devDependencies[$package] = $version;
                $this->line(" - Added: <info>{$package}: {$version}</info>");
                $updated = true;
            }
        }

        if ($updated) {
            file_put_contents(
                $packageJsonPath,
                json_encode($rootPackage, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            );
            $this->info('package.json updated successfully!');
            $this->info('Running npm install');
            exec('npm install');
        } else {
            $this->info('All required dependencies are already in your package.json.');
        }

        return 0;
    }

    protected function createStore(): void
    {
        Store::forceCreate(['url' => config('app.url')]);
    }

    protected function createTaxClass()
    {
        $params = [
            'name' => 'VAT 23%',
            'code' => 'default',
            'rate' => '23',
        ];
        TaxClass::query()->where($params)->firstOrCreate($params);

    }

    protected function createAttributes()
    {
        $attributesData = [
            [
                'name' => 'Nazwa',
                'code' => 'name',
                'backend_type' => 'string',
                'frontend_type' => 'text',
                'is_filterable' => true,
                'is_searchable' => true,
                'is_system' => true,
                'is_required' => true,
                'is_used_in_list' => true,
                'is_visible_in_details' => false,
                'entity_type' => Attribute::ENTITY_TYPE_CATEGORY,
                'sort_order' => 5
            ],
            [
                'name' => 'Nazwa',
                'code' => 'name',
                'backend_type' => 'string',
                'frontend_type' => 'text',
                'is_filterable' => true,
                'is_searchable' => true,
                'is_system' => true,
                'is_required' => true,
                'is_used_in_list' => true,
                'is_visible_in_details' => false,
                'is_used_on_product_page' => true,
                'entity_type' => Attribute::ENTITY_TYPE_PRODUCT,
                'sort_order' => 5
            ],
            [
                'name' => 'Opis',
                'code' => 'description',
                'backend_type' => 'text',
                'frontend_type' => 'textarea',
                'is_filterable' => false,
                'is_searchable' => true,
                'is_system' => true,
                'is_required' => false,
                'is_used_in_list' => false,
                'is_visible_in_details' => false,
                'entity_type' => Attribute::ENTITY_TYPE_CATEGORY,
                'sort_order' => 10
            ],
            [
                'name' => 'Opis',
                'code' => 'description',
                'backend_type' => 'text',
                'frontend_type' => 'textarea',
                'is_filterable' => false,
                'is_searchable' => true,
                'is_system' => true,
                'is_required' => false,
                'is_used_in_list' => false,
                'is_visible_in_details' => false,
                'is_used_on_product_page' => true,
                'entity_type' => Attribute::ENTITY_TYPE_PRODUCT,
                'sort_order' => 10
            ],
            [
                'name' => 'Krótki opis',
                'code' => 'short_description',
                'backend_type' => 'text',
                'frontend_type' => 'textarea',
                'is_filterable' => false,
                'is_searchable' => true,
                'is_system' => true,
                'is_required' => false,
                'is_used_in_list' => false,
                'is_visible_in_details' => false,
                'is_used_on_product_page' => true,
                'sort_order' => 20,
                'entity_type' => Attribute::ENTITY_TYPE_PRODUCT,
            ],
            [
                'name' => 'Aktywny',
                'code' => 'is_active',
                'backend_type' => 'bool',
                'frontend_type' => 'bool',
                'is_filterable' => false,
                'is_searchable' => false,
                'is_system' => true,
                'is_required' => false,
                'is_used_in_list' => false,
                'is_visible_in_details' => false,
                'entity_type' => Attribute::ENTITY_TYPE_CATEGORY,
                'sort_order' => 1
            ],
            [
                'name' => 'Aktywny',
                'code' => 'is_active',
                'backend_type' => 'bool',
                'frontend_type' => 'bool',
                'is_filterable' => false,
                'is_searchable' => false,
                'is_system' => true,
                'is_required' => false,
                'is_used_in_list' => false,
                'is_visible_in_details' => false,
                'entity_type' => Attribute::ENTITY_TYPE_PRODUCT,
                'sort_order' => 1
            ],
            [
                'name' => 'Pokaż w menu',
                'code' => 'display_in_menu',
                'backend_type' => 'bool',
                'frontend_type' => 'bool',
                'is_filterable' => true,
                'is_searchable' => true,
                'is_visible_in_details' => false,
                'is_system' => true,
                'is_required' => true,
                'entity_type' => Attribute::ENTITY_TYPE_CATEGORY,
                'sort_order' => 10
            ]
        ];
        foreach ($attributesData as $attributeData) {
            $attribute = Attribute::query()
                ->where('entity_type', $attributeData['entity_type'])
                ->where('code', $attributeData['code'])
                ->first();
            if ($attribute) {
                $attribute->update($attributeData);
            } else {
                Attribute::forceCreate($attributeData);
            }
        }
    }

    protected function importCeneoCategories()
    {
        $xmlString = file_get_contents(__DIR__ . '/../../Database/data/kategorie-ceneo.xml');

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NONET);
        if ($xml === false) {
            throw new RuntimeException('Błąd XML: ' . implode('; ', array_map(fn($e) => $e->message, libxml_get_errors())));
        }

        $data = json_decode(json_encode($xml, JSON_UNESCAPED_UNICODE), true);
        foreach ($data['Category'] as $row) {
            $this->importCeneoCategory($row);
        }

    }

    protected function importCeneoCategory($row, $path = null, $parentId = null)
    {
        $params = ['external_id' => $row['Id']];
        $cat = CeneoCategory::query()->where($params)->firstOrNew($params);
        $cat->name = $row['Name'];
        $cat->parent_id = $parentId;
        $cat->save();
        $cat->path = trim(implode('/', [$path, $cat->id]), '/');
        $cat->save();
        if ($row['Subcategories']['Category'] ?? false) {
            foreach ($row['Subcategories']['Category'] as $subCat) {
                $this->importCeneoCategory($subCat, $cat->path, $cat->id);
            }
        }
    }
}