<?php

namespace Shopen\Http\Controllers\Frontend\User;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Requests\Frontend\User\UpdateSettingsRequest;

class UserSettingsIndexController
{
    public function index(): Response
    {
        return Inertia::render('Frontend/User/Settings/Index', []);
    }

    public function update(UpdateSettingsRequest $request)
    {
        $user = Auth::user();
        $user->update($request->validated());

        return back()->with('success', 'Twoje dane zostały zmienione!');
    }
}