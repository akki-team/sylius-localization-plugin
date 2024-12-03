<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\Cache;

use Akki\SyliusLocalizationPlugin\Cache\Resolver\CacheKeyResolverInterface;
use Akki\SyliusLocalizationPlugin\Entity\Localization\LocalizedEntryInterface;
use Symfony\Contracts\Cache\CacheInterface;

final class LocalizedEntryCacheClearer implements LocalizedEntryCacheClearerInterface
{
    public function __construct(
        private readonly CacheKeyResolverInterface $cacheKeyResolver,
        private readonly CacheInterface            $cache,
    )
    {
    }

    public function clear(LocalizedEntryInterface $localizedEntry): void
    {
        foreach ($localizedEntry->getTranslations() as $translation) {
            $cacheKey = $this->cacheKeyResolver->getKey($localizedEntry->getKey(), $localizedEntry->getDomain(), $translation->getLocale(), $localizedEntry->getChannel()->getCode());
            $this->cache->delete($cacheKey);
        }
    }
}
