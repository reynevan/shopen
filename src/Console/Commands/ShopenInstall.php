<?php

namespace Shopen\Console\Commands;

use Illuminate\Console\Command;

class ShopenInstall extends Command
{
    protected $signature = 'shopen:install';
    protected $description = 'Installs frontend dependencies required by Shopen Core.';

    public function handle()
    {
        $this->info('Adding Shopen Core frontend dependencies to package.json...');

        $packageJsonPath = base_path('package.json');
        $shopenPackageJsonPath = __DIR__.'/../../../package.json'; // Dostosuj ścieżkę

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

        // Dodajemy jako devDependencies, co jest standardem w Laravel
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
            $this->warn('Please run "npm install" or "yarn install" to install the new dependencies.');
        } else {
            $this->info('All required dependencies are already in your package.json.');
        }

        return 0;
    }
}