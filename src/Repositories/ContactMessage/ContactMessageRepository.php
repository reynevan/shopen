<?php

namespace Shopen\Repositories\ContactMessage;

use App\Support\ProductSorting\ProductSortRegistry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Shopen\Enums\ContactMessage\Status;
use Shopen\Models\Category\Category;
use Shopen\Models\ContactMessage\ContactMessage;
use Shopen\Models\Product\Price\ProductPriceRule;
use Shopen\Models\Product\Product;
use Shopen\Repositories\Product\ProductAttributeRepository;
use Shopen\Services\CustomAttributesService;
use Shopen\Services\SearchService\SearchService;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ContactMessageRepository
{

    public function getPaginated($sortField, $sortDir, $status = null)
    {
        return ContactMessage::query()
            ->when($status && in_array($status, array_keys(Status::options())), function (Builder $query) use ($status) {
                    $query->where('status', $status);
            })
            ->orderBy($sortField, $sortDir)
            ->paginate()
            ->withQueryString();
    }
}