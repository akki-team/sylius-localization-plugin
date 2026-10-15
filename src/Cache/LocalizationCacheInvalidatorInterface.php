<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\Cache;

interface LocalizationCacheInvalidatorInterface
{
    /**
     * @param iterable<array{id: string, domain: string, locale: string, channelCode: string}> $messages
     */
    public function invalidate(iterable $messages): void;
}
