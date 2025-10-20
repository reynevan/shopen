<?php

namespace Shopen\Http\Controllers\Admin\PromoCode;

use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Controller;
use Shopen\Http\Resources\Admin\PromoCode\PromoCodeResource;
use Shopen\Models\PromoCode\PromoCode;
use Shopen\Repositories\PromoCodeRepository;

class PromoCodesIndexController extends Controller
{
    public function __construct(
        private readonly PromoCodeRepository $promoCodeRepository,
    )
    {}

    public function index(): Response
    {
        $promoCodes = $this->promoCodeRepository->getPaginated(request('sort', 'id'), request('dir', 'asc'), request('q'));

        return Inertia::render('Admin/PromoCode/Index', [
            'promoCodes' => PromoCodeResource::collection($promoCodes),
            'sort' => request('sort', 'id'),
            'dir' => request('dir', 'asc'),
            'q' => request('q')
        ]);
    }
}