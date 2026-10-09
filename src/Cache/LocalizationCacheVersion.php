<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\Cache;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Service\ResetInterface;

/**
 * Jeton de version des traductions, stocké dans le pool de cache partagé (Redis par exemple).
 *
 * Il entre dans la clé des copies locales (APCu) de LocalCacheMessageProvider : le changer rend
 * caduques les copies de tous les serveurs dès leur requête suivante, sans avoir à les joindre.
 * Il est changé à chaque invalidation (LocalizationCacheInvalidator) et disparaît avec le pool
 * quand celui-ci est vidé.
 */
final class LocalizationCacheVersion implements ResetInterface
{
    public const CACHE_KEY = 'akki_localization_version';

    private ?string $current = null;

    private bool $unavailable = false;

    // Invalidations faites par ce processus : la mémoire de LocalCacheMessageProvider s'y aligne.
    private int $generation = 0;

    public function __construct(
        private readonly CacheItemPoolInterface|CacheInterface $cache,
    )
    {
    }

    /**
     * Lu une seule fois par requête. Null si le pool ne peut pas conserver le jeton (cache
     * injoignable) : un jeton propre à chaque requête remplirait les copies locales d'entrées
     * jamais relues, l'appelant s'en passe donc jusqu'à la requête suivante.
     */
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
            // Sans nouveau jeton, les copies locales resteraient valides jusqu'à leur expiration.
            // Sans jeton du tout, current() ne pourra pas en recréer un tant que l'écriture échoue,
            // et les copies locales sont ignorées.
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
