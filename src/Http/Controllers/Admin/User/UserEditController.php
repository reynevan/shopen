<?php

namespace Shopen\Http\Controllers\Admin\User;

use Inertia\Inertia;
use Shopen\Http\Resources\Admin\User\CustomerResource;
use Shopen\Http\Resources\Order\OrderResource;
use Shopen\Models\User;

class UserEditController
{

    public function edit(User $user)
    {

        $orders = $user->orders()->latest()->get();

        return Inertia::render('Admin/User/Edit', [
            'user' => CustomerResource::make($user),
            'orders' => OrderResource::collection($orders),
        ]);

    }
}