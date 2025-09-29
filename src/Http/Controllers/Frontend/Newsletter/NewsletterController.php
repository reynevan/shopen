<?php

namespace Shopen\Http\Controllers\Frontend\Newsletter;

use Illuminate\Http\JsonResponse;
use Shopen\Http\Requests\Frontend\Newsletter\NewsletterSubscribeRequest;
use Shopen\Services\Newsletter\NewsletterServiceInterface;

class NewsletterController
{
    public function __construct(
        private NewsletterServiceInterface $newsletterService
    ) {}

    public function subscribe(NewsletterSubscribeRequest $request): JsonResponse
    {
        $email = $request->validated('email');
        $attributes = $request->validated('attributes', []);

        $success = $this->newsletterService->subscribe($email, $attributes);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Dziękujemy za subskrypcję! Sprawdź swoją skrzynkę e-mail i potwierdź subskrypcję klikając w link.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Wystąpił błąd podczas zapisywania na newsletter. Spróbuj ponownie.'
        ], 422);
    }

    public function unsubscribe(NewsletterSubscribeRequest $request): JsonResponse
    {
        $email = $request->validated('email');

        $success = $this->newsletterService->unsubscribe($email);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Zostałeś wypisany z newslettera.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Wystąpił błąd podczas wypisywania z newslettera.'
        ], 422);
    }

    public function confirmed()
    {
        return inertia('Frontend/Newsletter/Confirmed');
    }
}