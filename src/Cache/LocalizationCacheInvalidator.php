<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\Cache;

use Akki\SyliusLocalizationPlugin\Cache\Resolver\CacheKeyResolverInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * Vide des traductions du cache partagé, puis change le jeton de version pour rendre caduques
 * les copies locales (APCu) de tous les serveurs.
 */
final class LocalizationCacheInvalidator
{
    private const DELETE_BATCH_SIZE = 500;

    public function __construct(
        private readonly CacheKeyResolverInterface $cacheKeyResolver,
        private readonly CacheInterface            $cache,
        private readonly LocalizationCacheVersion  $version,
        private readonly CacheKeySanitizer         $cacheKeySanitizer = new CacheKeySanitizer(),
    )
    {
    }

    /**
     * @param iterable<array{id: string, domain: string, locale: string, channelCode: string}> $messages
     */
    public function invalidate(iterable $messages): void
    {
        try {
            $keys = [];

            foreach ($messages as $message) {
                $keys[] = $this->cacheKeySanitizer->sanitize($this->cacheKeyResolver->getKey($message['id'], $message['domain'], $message['locale'], $message['channelCode']));
            }

            $this->delete(array_values(array_unique($keys)));
        } finally {
            $this->version->bump();
        }
    }

    /**
     * @param list<string> $keys
     */
    private function delete(array $keys): void
    {
        if ($this->cache instanceof CacheItemPoolInterface) {
            foreach (array_chunk($keys, self::DELETE_BATCH_SIZE) as $batch) {
                $this->cache->deleteItems($batch);
            }

            return;
        }

        foreach ($keys as $key) {
            $this->cache->delete($key);
        }
    }
}
