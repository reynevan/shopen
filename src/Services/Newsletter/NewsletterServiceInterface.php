<?php

namespace Shopen\Services\Newsletter;

interface NewsletterServiceInterface
{
    public function subscribe(string $email, array $attributes = []): bool;
    public function unsubscribe(string $email): bool;
    public function isSubscribed(string $email): bool;
}