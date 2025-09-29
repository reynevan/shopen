<?php

namespace Shopen\Http\Controllers\Admin\TaxClass;

use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Controller;
use Shopen\Http\Resources\Admin\TaxClass\TaxClassResource;
use Shopen\Repositories\TaxClass\TaxClassRepository;

class TaxClassIndexController extends Controller
{
    public function __construct(
        private readonly TaxClassRepository $taxClassRepository,
    )
    {}

    public function index(): Response
    {
        $taxClasses = $this->taxClassRepository->getPaginated(request('sort', 'id'), request('dir', 'asc'), request('q'));

        return Inertia::render('Admin/TaxClass/Index', [
            'taxClasses' => TaxClassResource::collection($taxClasses),
            'sort' => request('sort', 'id'),
            'dir' => request('dir', 'desc'),
            'q' => request('q')
        ]);
    }
}