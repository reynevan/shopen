<?php

namespace Shopen\Providers;

use Shopen\Services\Newsletter\NewsletterServiceInterface;
use Shopen\Services\Newsletter\MailchimpNewsletterService;
use Shopen\Services\Newsletter\BrevoNewsletterService;
use Illuminate\Support\ServiceProvider;

class NewsletterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(NewsletterServiceInterface::class, function ($app) {
            $provider = config('newsletter.provider', 'mailchimp');

            return match ($provider) {
                'mailchimp' => new MailchimpNewsletterService(),
                'brevo' => new BrevoNewsletterService(),
                default => throw new \InvalidArgumentException("Unsupported newsletter provider: {$provider}")
            };
        });
    }
}