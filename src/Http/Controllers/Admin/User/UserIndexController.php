<?php

namespace Shopen\Http\Controllers\Admin\User;

use Inertia\Inertia;
use Shopen\Http\Resources\Admin\User\CustomerResource;
use Shopen\Repositories\User\UserRepository;

class UserIndexController
{

    public function __construct(
        protected readonly UserRepository $userRepository
    )
    {}

    public function index()
    {
        $users = $this->userRepository->getPaginated(request('sort', 'id'), request('dir', 'asc'), request('q'));

        return Inertia::render('Admin/User/Index', [
            'users' => CustomerResource::collection($users),
            'sort' => request('sort', 'id'),
            'dir' => request('dir', 'desc'),
            'q' => request('q')
        ]);

    }
}