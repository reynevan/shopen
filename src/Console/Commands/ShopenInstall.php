<?php

namespace Shopen\Console\Commands;

use Illuminate\Console\Command;
use Shopen\Models\Attribute\Attribute;
use Shopen\Models\User;

class ShopenInstall extends Command
{
    protected $signature = 'shopen:install';
    protected $description = 'Installs Shopen';

    public function handle()
    {
        $this->installFrontendDependencies();
        $this->runCommand('migrate', [], $this->output);
        $this->createAttributes();
        $admin = User::query()->where('role', User::ROLE_ADMIN)->first();
        if (!$admin) {
            $this->runCommand('shopen:create-admin-user', [], $this->output);
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

    protected function createAttributes()
    {
        $types = [Attribute::ENTITY_TYPE_PRODUCT, Attribute::ENTITY_TYPE_CATEGORY];
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
                'sort_order' => 5
            ],
            [
                'name' => 'Opis',
                'code' => 'description',
                'backend_type' => 'text',
                'frontend_type' => 'text',
                'is_filterable' => false,
                'is_searchable' => true,
                'is_system' => true,
                'is_required' => false,
                'is_used_in_list' => false,
                'sort_order' => 10
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
                'sort_order' => 1
            ],
            [
                'name' => 'Pokaż w menu',
                'code' => 'display_in_menu',
                'backend_type' => 'bool',
                'frontend_type' => 'bool',
                'is_filterable' => true,
                'is_searchable' => true,
                'is_system' => true,
                'is_required' => true,
                'entity_type' => Attribute::ENTITY_TYPE_CATEGORY,
                'sort_order' => 10
            ]
        ];
        foreach ($types as $type) {
            foreach ($attributesData as $attributeData) {
                $attributeData['entity_type'] = $attributeData['entity_type'] ?? $type;
                $attribute = Attribute::query()->where('code', $attributeData['code'])->first();
                if ($attribute) {
                    $attribute->update($attributeData);
                } else {
                    Attribute::forceCreate($attributeData);
                }
            }
        }
    }

}