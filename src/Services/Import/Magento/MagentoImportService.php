<?php

namespace Shopen\Services\Import\Magento;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class MagentoImportService
{
    use CategoriesImporter, ProductsImporter;

    protected string $connectionName = 'magento_import';

    public function __construct(
        protected string $url,
        protected string $database,
        protected string $username,
        protected string $password,
        protected string $host = 'localhost',
    )
    {
        $this->setupConnection();
    }

    protected function setupConnection(): void
    {
        Config::set("database.connections.{$this->connectionName}", [
            'driver' => 'mysql',
            'host' => $this->host,
            'database' => $this->database,
            'username' => $this->username,
            'password' => $this->password,
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ]);
    }

    public function __destruct()
    {
        DB::purge($this->connectionName);
    }

    protected function tableExists(string $table): bool
    {
        try {
            DB::connection($this->connectionName)->select("SELECT 1 FROM {$table} LIMIT 1");
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
}