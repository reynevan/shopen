<?php

namespace Shopen\Services;

use Shopen\Models\Category\Category;
use Shopen\Models\Product\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

class SitemapService
{
    private const SITEMAP_LIMIT = 50000; // Limit URL-i na sitemap
    private const SITEMAP_PATH = 'sitemaps/';

    public function generateSitemap(): string
    {
        $this->clearOldSitemaps();

        $sitemaps = [];

        // Generuj sitemap dla stron statycznych
        $staticSitemap = $this->generateStaticSitemap();
        if ($staticSitemap) {
            $sitemaps[] = $staticSitemap;
        }

        // Generuj sitemaps dla kategorii
        $categorySitemaps = $this->generateCategorySitemaps();
        $sitemaps = array_merge($sitemaps, $categorySitemaps);

        // Generuj sitemaps dla produktów
        $productSitemaps = $this->generateProductSitemaps();
        $sitemaps = array_merge($sitemaps, $productSitemaps);

        // Generuj sitemap index
        return $this->generateSitemapIndex($sitemaps);
    }

    private function generateStaticSitemap(): ?string
    {
        $urls = [
            [
                'loc' => URL::to('/'),
                'changefreq' => 'daily',
                'priority' => '1.0',
                'lastmod' => Carbon::now()->toISOString()
            ],
           /* [
                'loc' => URL::to('/login'),
                'changefreq' => 'monthly',
                'priority' => '0.3',
                'lastmod' => Carbon::now()->toISOString()
            ],
            [
                'loc' => URL::to('/register'),
                'changefreq' => 'monthly',
                'priority' => '0.3',
                'lastmod' => Carbon::now()->toISOString()
            ],
            [
                'loc' => URL::to('/contact'),
                'changefreq' => 'monthly',
                'priority' => '0.5',
                'lastmod' => Carbon::now()->toISOString()
            ],
            [
                'loc' => URL::to('/about'),
                'changefreq' => 'monthly',
                'priority' => '0.5',
                'lastmod' => Carbon::now()->toISOString()
            ],
            [
                'loc' => URL::to('/privacy-policy'),
                'changefreq' => 'yearly',
                'priority' => '0.3',
                'lastmod' => Carbon::now()->toISOString()
            ],
            [
                'loc' => URL::to('/terms'),
                'changefreq' => 'yearly',
                'priority' => '0.3',
                'lastmod' => Carbon::now()->toISOString()
            ]*/
        ];

        if (empty($urls)) {
            return null;
        }

        $filename = 'sitemap-static.xml';
        $this->saveSitemap($urls, $filename);

        return $filename;
    }

    private function generateCategorySitemaps(): array
    {
        $sitemaps = [];
        $categories = Category::query()->where('is_active', 1)
            ->with(['urlRewrite'])
            ->orderBy('id')
            ->get();

        $chunks = $categories->chunk(self::SITEMAP_LIMIT);

        foreach ($chunks as $index => $chunk) {
            $urls = [];

            foreach ($chunk as $category) {
                $url = $category->getUrl();
                if ($url) {
                    $urls[] = [
                        'loc' => $url,
                        'changefreq' => 'weekly',
                        'priority' => $this->getCategoryPriority($category),
                        'lastmod' => $category->updated_at->toISOString()
                    ];
                }
            }

            if (!empty($urls)) {
                $filename = 'sitemap-categories-' . ($index + 1) . '.xml';
                $this->saveSitemap($urls, $filename);
                $sitemaps[] = $filename;
            }
        }

        return $sitemaps;
    }

    private function generateProductSitemaps(): array
    {
        $sitemaps = [];

        $products = Product::query()->whereHas('price')
            ->where('in_stock', 1)
            ->with(['urlRewrite'])
            ->orderBy('id')
            ->get();

        $chunks = $products->chunk(self::SITEMAP_LIMIT);

        foreach ($chunks as $index => $chunk) {
            $urls = [];

            foreach ($chunk as $product) {
                $url = $product->getUrl();
                if ($url) {
                    $urls[] = [
                        'loc' => $url,
                        'changefreq' => 'daily',
                        'priority' => '0.8',
                        'lastmod' => $product->updated_at->toISOString()
                    ];
                }
            }

            if (!empty($urls)) {
                $filename = 'sitemap-products-' . ($index + 1) . '.xml';
                $this->saveSitemap($urls, $filename);
                $sitemaps[] = $filename;
            }
        }

        return $sitemaps;
    }

    private function getCategoryPriority(Category $category): string
    {
        if ($category->level === 0) {
            return '0.9';
        } elseif ($category->level === 1) {
            return '0.7';
        } else {
            return '0.5';
        }
    }

    private function saveSitemap(array $urls, string $filename): void
    {
        $xml = $this->generateSitemapXml($urls);
        Storage::disk('public')->put(self::SITEMAP_PATH . $filename, $xml);
    }

    private function generateSitemapXml(array $urls): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        foreach ($urls as $url) {
            $xml .= '  <url>' . PHP_EOL;
            $xml .= '    <loc>' . htmlspecialchars($url['loc']) . '</loc>' . PHP_EOL;
            $xml .= '    <lastmod>' . $url['lastmod'] . '</lastmod>' . PHP_EOL;
            $xml .= '    <changefreq>' . $url['changefreq'] . '</changefreq>' . PHP_EOL;
            $xml .= '    <priority>' . $url['priority'] . '</priority>' . PHP_EOL;
            $xml .= '  </url>' . PHP_EOL;
        }

        $xml .= '</urlset>';

        return $xml;
    }

    private function generateSitemapIndex(array $sitemaps): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        foreach ($sitemaps as $sitemap) {
            $xml .= '  <sitemap>' . PHP_EOL;
            $xml .= '    <loc>' . URL::to('/storage/' . self::SITEMAP_PATH . $sitemap) . '</loc>' . PHP_EOL;
            $xml .= '    <lastmod>' . Carbon::now()->toISOString() . '</lastmod>' . PHP_EOL;
            $xml .= '  </sitemap>' . PHP_EOL;
        }

        $xml .= '</sitemapindex>';

        $indexFilename = 'sitemap.xml';
        Storage::disk('public')->put($indexFilename, $xml);

        return $indexFilename;
    }

    private function clearOldSitemaps(): void
    {
        $files = Storage::disk('public')->files(self::SITEMAP_PATH);
        foreach ($files as $file) {
            Storage::disk('public')->delete($file);
        }

        // Usuń główny sitemap index
        if (Storage::disk('public')->exists('sitemap.xml')) {
            Storage::disk('public')->delete('sitemap.xml');
        }
    }

    public function getSitemapUrl(): string
    {
        return URL::to('/storage/sitemap.xml');
    }
}