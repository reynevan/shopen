<?php

namespace Shopen\Http\Controllers\Frontend\User;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Requests\Frontend\User\UpdateSettingsRequest;
use Shopen\Services\Newsletter\NewsletterServiceInterface;

class UserSettingsIndexController
{
    public function __construct(
        private NewsletterServiceInterface $newsletterService,
    )
    {}

    public function index(): Response
    {
        return Inertia::render('Frontend/User/Settings/Index', [
            'newsletter_active' => $this->newsletterService->isSubscribed(Auth::user()->email),
        ]);
    }

    public function update(UpdateSettingsRequest $request)
    {
        $user = Auth::user();
        $user->update($request->validated());

        $isSubscribed = $this->newsletterService->isSubscribed($user->email);

        if ($request->newsletter_active && !$isSubscribed) {
            $this->newsletterService->subscribe($user->email);
        }
        if (!$request->newsletter_active && $isSubscribed) {
            $this->newsletterService->unsubscribe($user->email);
        }

        return back()->with('success', 'Twoje dane zostały zmienione!');
    }
}