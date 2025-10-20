<?php

namespace Shopen\Console\Commands;


use Illuminate\Console\Command;
use Shopen\Services\Import\Magento\MagentoImportService;
use src\Mail\Order\OrderPlaced;

class ImportMagento extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shopen:import:magento  
                            {database : Database name} 
                            {username : Database username} 
                            {password : Database password}
                            {host=localhost : Database host}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import data from magento database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Magento attributes import...');

        $service = new MagentoImportService(
            host: $this->argument('host'),
            database: $this->argument('database'),
            username: $this->argument('username'),
            password: $this->argument('password')
        );

        $attributes = $service->importAttributes();

    }
}
