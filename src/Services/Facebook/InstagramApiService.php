<?php

namespace Shopen\Services\Facebook;

use GuzzleHttp\Client;

class InstagramApiService
{
    public function __construct(protected Client $client)
    {}

    public function exchangeCodeForUserToken(string $code): array
    {

        $url = 'https://graph.facebook.com/v20.0/oauth/access_token'
            . '?client_id=' . urlencode(config('services.instagram.client_id'))
            . '&redirect_uri=' . urlencode(config('services.instagram.redirect'))
            . '&client_secret=' . urlencode(config('services.instagram.client_secret'))
            . '&code=' . urlencode($code);

        $response = $this->client->get($url);

        return json_decode($response->getBody()->getContents(), true) ?: [];
    }

    public function exchangeForLongLivedUserToken(string $userToken): array
    {

        $url = 'https://graph.facebook.com/v20.0/oauth/access_token'
            . '?grant_type=fb_exchange_token'
            . '&client_id=' . urlencode(config('services.instagram.client_id'))
            . '&client_secret=' . urlencode(config('services.instagram.client_secret'))
            . '&fb_exchange_token=' . urlencode($userToken);

        $response = $this->client->get($url);
        return json_decode($response->getBody()->getContents(), true) ?: [];
    }

    public function getUserPages(string $userAccessToken): array
    {
        $url = 'https://graph.facebook.com/v20.0/me/accounts'
            . '?fields=id,name,access_token'
            . '&access_token=' . urlencode($userAccessToken);

        $response = $this->client->get($url);
        $resp = json_decode($response->getBody()->getContents(), true) ?: [];
        return $resp['data'] ?? [];
    }

    public function getIgUserIdByPage(string $pageId, string $pageAccessToken): ?string
    {
        $url = 'https://graph.facebook.com/v20.0/' . urlencode($pageId)
            . '?fields=instagram_business_account'
            . '&access_token=' . urlencode($pageAccessToken);

        $response = $this->client->get($url);
        $data = json_decode($response->getBody()->getContents(), true) ?: [];
        return $data['instagram_business_account']['id'] ?? null;
    }

    public function getLongLivedPageToken(string $pageId, string $longLivedUserToken): ?string
    {
        $url = 'https://graph.facebook.com/v20.0/me/accounts'
            . '?fields=id,name,access_token'
            . '&access_token=' . urlencode($longLivedUserToken);

        $response = $this->client->get($url);
        $resp = json_decode($response->getBody()->getContents(), true) ?: [];
        $pages = $resp['data'] ?? [];
        foreach ($pages as $p) {
            if (!empty($p['id']) && $p['id'] === $pageId && !empty($p['access_token'])) {
                return $p['access_token'];
            }
        }
        return null;
    }

    public function refreshLongLivedUserToken(string $longLivedUserToken): array
    {
        $url = 'https://graph.facebook.com/v20.0/oauth/access_token'
            . '?grant_type=fb_exchange_token'
            . '&client_id=' . urlencode(config('services.instagram.client_id'))
            . '&client_secret=' . urlencode(config('services.instagram.client_secret'))
            . '&fb_exchange_token=' . urlencode($longLivedUserToken);

        $response = $this->client->get($url);
        return json_decode($response->getBody()->getContents(), true) ?: [];
    }

    public function fetchRecentMedia(string $igUserId, string $pageAccessToken, int $limit = 10): array
    {
        $fields = 'id,caption,media_type,media_url,permalink,timestamp,thumbnail_url';
        $url = 'https://graph.facebook.com/v20.0/' . urlencode($igUserId) . '/media'
            . '?fields=' . urlencode($fields)
            . '&limit=' . $limit
            . '&access_token=' . urlencode($pageAccessToken);

        $response = $this->client->get($url);
        $data = json_decode($response->getBody()->getContents(), true) ?: [];
        return $data['data'] ?? [];
    }
}