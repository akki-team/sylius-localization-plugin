<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\Cache;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Service\ResetInterface;

final class LocalizationCacheVersion implements LocalizationCacheVersionInterface, ResetInterface
{
    public const CACHE_KEY = 'akki_localization_version';

    private ?string $current = null;

    private bool $unavailable = false;

    private int $generation = 0;

    public function __construct(
        private readonly CacheItemPoolInterface|CacheInterface $cache,
    )
    {
    }

    public function current(): ?string
    {
        if (null === $this->current && false === $this->unavailable) {
            if (false === $this->cache instanceof CacheItemPoolInterface) {
                return $this->current = (string)$this->cache->get(self::CACHE_KEY, fn(): string => $this->generate());
            }

            $item = $this->cache->getItem(self::CACHE_KEY);

            if (true === $item->isHit()) {
                $this->current = (string)$item->get();
            } elseif (true === $this->cache->save($item->set($this->generate()))) {
                $this->current = (string)$item->get();
            } else {
                $this->unavailable = true;
            }
        }

        return $this->current;
    }

    public function bump(): void
    {
        ++$this->generation;

        if (false === $this->cache instanceof CacheItemPoolInterface) {
            $this->cache->delete(self::CACHE_KEY);
        } elseif (false === $this->cache->save($this->cache->getItem(self::CACHE_KEY)->set($this->generate()))) {
            $this->cache->deleteItem(self::CACHE_KEY);
        }

        $this->reset();
    }

    public function getGeneration(): int
    {
        return $this->generation;
    }

    public function reset(): void
    {
        $this->current = null;
        $this->unavailable = false;
    }

    private function generate(): string
    {
        return bin2hex(random_bytes(8));
    }
}
