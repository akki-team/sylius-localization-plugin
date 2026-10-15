<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\Cache;

use Symfony\Contracts\Cache\ItemInterface;

final class CacheKeySanitizer
{
    public function sanitize(string $key): string
    {
        return preg_replace('/[' . preg_quote(ItemInterface::RESERVED_CHARACTERS, '/') . ']/', '_', $key);
    }
}
