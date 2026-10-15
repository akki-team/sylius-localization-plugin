<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\Cache;

use Akki\SyliusLocalizationPlugin\Entity\Localization\LocalizedEntryInterface;

interface LocalizedEntryCacheClearerInterface
{
    public function clear(LocalizedEntryInterface $localizedEntry): void;

    /**
     * @param iterable<LocalizedEntryInterface> $localizedEntries
     */
    public function clearMany(iterable $localizedEntries): void;
}
