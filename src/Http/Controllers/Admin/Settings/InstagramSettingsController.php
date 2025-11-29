<?php

namespace Shopen\Http\Controllers\Admin\Settings;

use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Services\ConfigService;
use Shopen\Services\Facebook\InstagramApiService;

class InstagramSettingsController
{
    public function __construct(
        protected InstagramApiService $api,
        protected ConfigService $config
    )
    {}

    public function index(): Response
    {
        return Inertia::render('Admin/Settings/Instagram', [
            'connectUrl' => $this->getAuthUrl()
        ]);
    }

    public function callback(): RedirectResponse
    {
        $userTokenResp = $this->api->exchangeCodeForUserToken(request('code'));
        if (empty($userTokenResp['access_token'])) {
            throw new \Exception('Nie udało się uzyskać user access token.');
        }
        $userToken = $userTokenResp['access_token'];
        $this->config->save('instagram/access_token', $userToken);

        $lluResp = $this->api->exchangeForLongLivedUserToken($userToken);
        $longLivedUserToken = $lluResp['access_token'] ?? $userToken;

        $pages = $this->api->getUserPages($longLivedUserToken);
        if (empty($pages)) {
            throw new \Exception('Brak stron Facebook dla tego konta.');
        }

        $selectedPageId = null;
        $selectedPageToken = null;
        $igUserId = null;

        // Znajdź pierwszą stronę z podłączonym kontem IG
        foreach ($pages as $page) {
            if (empty($page['id'])) {
                continue;
            }
            $pageId = $page['id'];

            // Upewnij się, że mamy page token z long-lived user tokena
            $pageToken = $this->api->getLongLivedPageToken($pageId, $longLivedUserToken);
            if (!$pageToken) {
                continue;
            }

            $candidateIgUserId = $this->api->getIgUserIdByPage($pageId, $pageToken);
            if ($candidateIgUserId) {
                $selectedPageId = $pageId;
                $selectedPageToken = $pageToken;
                $igUserId = $candidateIgUserId;
                break;
            }
        }

        if (!$igUserId || !$selectedPageToken) {
            throw new \Exception('Nie znaleziono konta Instagram Business połączonego ze stroną.');
        }

        // Zapisz Page ID, IG User ID i Page Token
        $this->config->save('instagram/page_id', $selectedPageId);
        $this->config->save('instagram/ig_user_id', $igUserId);
        $this->config->save('instagram/long_lived_token', $selectedPageToken);
        $this->config->save('instagram/long_lived_user_token', $longLivedUserToken);

        return redirect(route('admin.settings.instagram.index'))->with('success', 'Zmiany zostały zapisane');
    }

    protected function getAuthUrl(): string
    {
        $scope = [
            'pages_show_list',
            'instagram_basic',
            'business_management'
        ];
        $state = bin2hex(random_bytes(8));

        return sprintf(
            'https://www.facebook.com/v20.0/dialog/oauth?client_id=%s&redirect_uri=%s&state=%s&scope=%s&response_type=code',
            urlencode(config('services.instagram.client_id')),
            urlencode(config('services.instagram.redirect')),
            urlencode($state),
            urlencode(implode(',', $scope))
        );
    }
}