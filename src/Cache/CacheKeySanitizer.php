<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\Cache;

use Symfony\Contracts\Cache\ItemInterface;

/**
 * Clé de cache d'une traduction : les caractères réservés par Symfony Cache ({}()/\@:) sont
 * remplacés par « _ ». Même règle à l'écriture (CacheMessageProvider) et au vidage
 * (LocalizationCacheInvalidator), sinon les ids comme « This value should be of type {{ type }}. »
 * ne sont jamais vidés.
 */
final class CacheKeySanitizer
{
    public function sanitize(string $key): string
    {
        return preg_replace('/[' . preg_quote(ItemInterface::RESERVED_CHARACTERS, '/') . ']/', '_', $key);
    }
}
