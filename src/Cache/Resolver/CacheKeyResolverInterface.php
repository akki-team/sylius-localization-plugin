<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\Cache\Resolver;

interface CacheKeyResolverInterface
{
    public function getKey(string $id, string $domain, string $locale, string $channelCode): string;
}
