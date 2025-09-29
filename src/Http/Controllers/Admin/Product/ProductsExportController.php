<?php

namespace Shopen\Http\Controllers\Admin\Product;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Shopen\Jobs\ExportProducts;
use Shopen\Repositories\Product\ProductRepository;
use Shopen\Http\Resources\Admin\Product\BaseProductResource;

readonly class  ProductsExportController
    {
        public function __construct(protected ProductRepository $productRepository)
        {}

    public function index()
    {
        return Inertia::render('Admin/Product/Export', [
            'files' => $this->listExports()
        ]);
    }

    public function export()
    {
        ExportProducts::dispatch();

        return back();
    }

    protected function listExports(): array
    {
        $exportFiles = [];

        // Pobranie wszystkich plików z katalogu exports
        $files = Storage::disk('public')->files('exports');

        foreach ($files as $file) {
            // Sprawdzenie czy to plik CSV z eksportem produktów
            if (str_contains($file, 'products_export_') && str_ends_with($file, '.csv')) {
                $fileName = basename($file);
                $filePath = Storage::disk('public')->path($file);

                // Pobranie informacji o pliku
                $fileSize = Storage::disk('public')->size($file);
                $lastModified = Storage::disk('public')->lastModified($file);

                $exportFiles[] = [
                    'name' => $fileName,
                    'url' => Storage::disk('public')->url($file),
                    'size' => $this->formatFileSize($fileSize),
                    'created_at' => Carbon::createFromTimestamp($lastModified)->format('Y-m-d H:i:s'),
                    'download_url' => route('admin.products.export.download', ['filename' => $fileName])
                ];
            }
        }

        // Sortowanie po dacie utworzenia (najnowsze pierwsze)
        usort($exportFiles, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        return $exportFiles;
    }

    public function download($filename)
    {
        $filePath = "exports/{$filename}";

        if (!Storage::disk('public')->exists($filePath)) {
            abort(404, 'File not found');
        }

        // Sprawdzenie czy to prawidłowy plik eksportu
        if (!str_contains($filename, 'products_export_') || !str_ends_with($filename, '.csv')) {
            abort(403, 'Invalid file');
        }

        return Storage::disk('public')->download($filePath, $filename);
    }

    private function formatFileSize($bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' B';
        }
    }
}