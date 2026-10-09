<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\Cache;

use Psr\Cache\CacheItemPoolInterface;
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

    public function __construct(
        private readonly CacheItemPoolInterface $cache,
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
        $this->cache->save($this->cache->getItem(self::CACHE_KEY)->set($this->generate()));
        $this->reset();
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
