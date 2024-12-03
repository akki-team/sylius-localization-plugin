<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\Translation;

use Akki\SyliusLocalizationPlugin\Cache\Resolver\CacheKeyResolverInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final class CacheMessageProvider implements MessageProviderInterface
{
    public function __construct(
        private readonly MessageProviderInterface  $decorated,
        private readonly CacheKeyResolverInterface $cacheKeyResolver,
        private readonly CacheInterface            $cache,
    )
    {
    }

    public function getMessage(string $id, string $domain, string $locale, string $channelCode): ?string
    {
        $cacheKey = $this->cacheKeyResolver->getKey($id, $domain, $locale, $channelCode);
        $cacheKey = preg_replace('/[' . preg_quote(ItemInterface::RESERVED_CHARACTERS, '/') . ']/', '_', $cacheKey);

        return $this->cache->get($cacheKey, fn() => $this->decorated->getMessage($id, $domain, $locale, $channelCode));
    }

}
