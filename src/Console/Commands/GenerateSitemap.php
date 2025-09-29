<?php

namespace Shopen\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Shopen\Models\Order\Order;
use Shopen\Services\SitemapService;
use src\Mail\Order\OrderPlaced;

class GenerateSitemap extends Command
{
    public function __construct(protected SitemapService $sitemapService)
    {
        parent::__construct();
    }
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate sitemap';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->sitemapService->generateSitemap();
    }
}
