<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\Translation;

interface MessageProviderInterface
{
    public function getMessage(string $id, string $domain, string $locale, string $channelCode): ?string;
}
