<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\Cache;

interface LocalizationCacheVersionInterface
{
    public function current(): ?string;

    public function bump(): void;

    public function getGeneration(): int;
}
