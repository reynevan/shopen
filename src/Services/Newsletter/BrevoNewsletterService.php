<?php

namespace Shopen\Services\Newsletter;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BrevoNewsletterService implements NewsletterServiceInterface
{
    private string $apiKey;
    private int $listId;
    private string $baseUrl = 'https://api.brevo.com/v3';

    public function __construct()
    {
        $this->apiKey = config('newsletter.brevo.api_key');
        $this->listId = config('newsletter.brevo.list_id');
    }

    public function subscribe(string $email, array $attributes = []): bool
    {
        try {
            $response = Http::withHeaders([
                'api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/contacts/doubleOptinConfirmation", [
                'email' => $email,
                //'attributes' => $attributes,
                'includeListIds' => [$this->listId],
                'templateId' => 1, // Default double opt-in template
                'redirectionUrl' => route('newsletter.confirmed'),
            ]);

            if ($response->successful()) {
                Log::info('Newsletter subscription successful', ['email' => $email, 'provider' => 'brevo']);
                return true;
            }

            Log::error('Newsletter subscription failed', [
                'email' => $email,
                'provider' => 'brevo',
                'response' => $response->json()
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Newsletter subscription error', [
                'email' => $email,
                'provider' => 'brevo',
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function unsubscribe(string $email): bool
    {
        try {
            $response = Http::withHeaders([
                'api-key' => $this->apiKey,
            ])->delete("{$this->baseUrl}/contacts/{$email}");

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Newsletter unsubscribe error', [
                'email' => $email,
                'provider' => 'brevo',
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function isSubscribed(string $email): bool
    {
        try {
            $response = Http::withHeaders([
                'api-key' => $this->apiKey,
            ])->get("{$this->baseUrl}/contacts/{$email}");

            if ($response->successful()) {
                $data = $response->json();
                return in_array($this->listId, $data['listIds'] ?? []);
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}