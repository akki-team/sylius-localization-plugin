<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\Translation;

use Akki\SyliusLocalizationPlugin\Cache\CacheKeySanitizer;
use Akki\SyliusLocalizationPlugin\Cache\Resolver\CacheKeyResolverInterface;
use Symfony\Contracts\Cache\CacheInterface;

final class CacheMessageProvider implements MessageProviderInterface
{
    public function __construct(
        private readonly MessageProviderInterface  $decorated,
        private readonly CacheKeyResolverInterface $cacheKeyResolver,
        private readonly CacheInterface            $cache,
        private readonly CacheKeySanitizer         $cacheKeySanitizer = new CacheKeySanitizer(),
    )
    {
    }

    public function getMessage(string $id, string $domain, string $locale, string $channelCode): ?string
    {
        $cacheKey = $this->cacheKeySanitizer->sanitize($this->cacheKeyResolver->getKey($id, $domain, $locale, $channelCode));

        return $this->cache->get($cacheKey, fn() => $this->decorated->getMessage($id, $domain, $locale, $channelCode));
    }

}
