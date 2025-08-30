<?php

namespace Shopen\Services;

class OAuthProviderService
{
    public function getProviders(): array
    {
        $providers = ['google', 'facebook', 'twitter'];
        $result = [];
        foreach ($providers as $provider) {
            if (config('services.' . $provider . '.client_id') && config('services.' . $provider . '.client_secret') && config('services.' . $provider . '.redirect')) {
                $result[] = $provider;
            }
        }
        return $result;
    }
}