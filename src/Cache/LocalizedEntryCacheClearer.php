<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\Cache;

use Akki\SyliusLocalizationPlugin\Entity\Localization\LocalizedEntryInterface;

final class LocalizedEntryCacheClearer implements LocalizedEntryCacheClearerInterface
{
    public function __construct(
        private readonly LocalizationCacheInvalidator $localizationCacheInvalidator,
    )
    {
    }

    public function clear(LocalizedEntryInterface $localizedEntry): void
    {
        $this->localizationCacheInvalidator->invalidate($this->getMessages($localizedEntry));
    }

    /**
     * @return list<array{id: string, domain: string, locale: string, channelCode: string}>
     */
    private function getMessages(LocalizedEntryInterface $localizedEntry): array
    {
        $channelCode = $localizedEntry->getChannel()?->getCode();
        $id = $localizedEntry->getKey();
        $domain = $localizedEntry->getDomain();

        if (null === $channelCode || null === $id || null === $domain) {
            return [];
        }

        $messages = [];

        foreach ($localizedEntry->getTranslations() as $translation) {
            if (null === $translation->getLocale()) {
                continue;
            }

            $messages[] = ['id' => $id, 'domain' => $domain, 'locale' => $translation->getLocale(), 'channelCode' => $channelCode];
        }

        return $messages;
    }
}
