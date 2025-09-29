<?php

namespace Shopen\Services\Newsletter;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MailchimpNewsletterService implements NewsletterServiceInterface
{
    private string $apiKey;
    private string $listId;
    private string $serverPrefix;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('newsletter.mailchimp.api_key');
        $this->listId = config('newsletter.mailchimp.list_id');
        $this->serverPrefix = config('newsletter.mailchimp.server_prefix');
        $this->baseUrl = "https://{$this->serverPrefix}.api.mailchimp.com/3.0";
    }

    public function subscribe(string $email, array $attributes = []): bool
    {
        try {
            $subscriberHash = md5(strtolower($email));

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->put("{$this->baseUrl}/lists/{$this->listId}/members/{$subscriberHash}", [
                'email_address' => $email,
                'status' => 'pending',
                'language' => 'pl'
            ]);

            if ($response->successful()) {
                Log::info('Newsletter subscription successful', ['email' => $email, 'provider' => 'mailchimp']);
                return true;
            }

            Log::error('Newsletter subscription failed', [
                'email' => $email,
                'provider' => 'mailchimp',
                'response' => $response->json()
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Newsletter subscription error', [
                'email' => $email,
                'provider' => 'mailchimp',
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function unsubscribe(string $email): bool
    {
        try {
            $subscriberHash = md5(strtolower($email));

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->patch("{$this->baseUrl}/lists/{$this->listId}/members/{$subscriberHash}", [
                'status' => 'unsubscribed',
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Newsletter unsubscribe error', [
                'email' => $email,
                'provider' => 'mailchimp',
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function isSubscribed(string $email): bool
    {
        try {
            $subscriberHash = md5(strtolower($email));

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get("{$this->baseUrl}/lists/{$this->listId}/members/{$subscriberHash}");

            if ($response->successful()) {
                $data = $response->json();
                return in_array($data['status'], ['subscribed', 'pending']);
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}