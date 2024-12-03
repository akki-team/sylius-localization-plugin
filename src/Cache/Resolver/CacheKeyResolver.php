<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\Cache\Resolver;

final class CacheKeyResolver implements CacheKeyResolverInterface
{

    public function getKey(string $id, string $domain, string $locale, string $channelCode): string
    {
        if (true === str_contains($locale, '@')) {
            $locale = strstr($locale, '@', true);
        }

        return sprintf('%s.%s.%s.%s', $channelCode, $locale, $domain, $id);
    }
}
