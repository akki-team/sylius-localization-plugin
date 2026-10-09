<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\Cache;

use Akki\SyliusLocalizationPlugin\Entity\Localization\LocalizedEntryInterface;
use Doctrine\Persistence\ObjectRepository;
use Sylius\Component\Locale\Model\LocaleInterface;
use Symfony\Contracts\Service\ResetInterface;

final class LocalizedEntryCacheClearer implements LocalizedEntryCacheClearerInterface, ResetInterface
{
    /** @var list<string>|null */
    private ?array $shopLocaleCodes = null;

    public function __construct(
        private readonly LocalizationCacheInvalidator $localizationCacheInvalidator,
        private readonly ?ObjectRepository            $localeRepository = null,
    )
    {
    }

    public function clear(LocalizedEntryInterface $localizedEntry): void
    {
        $this->localizationCacheInvalidator->invalidate($this->getMessages($localizedEntry));
    }

    public function reset(): void
    {
        $this->shopLocaleCodes = null;
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

        // Les locales de l'entrée, et celles de la boutique : pour une locale sans ligne de
        // traduction, MessageProvider met en cache la valeur de la locale de repli sous la clé
        // de la locale demandée.
        $locales = $this->getShopLocaleCodes();

        foreach ($localizedEntry->getTranslations() as $translation) {
            if (null !== $translation->getLocale()) {
                $locales[] = $translation->getLocale();
            }
        }

        $messages = [];

        foreach (array_unique($locales) as $locale) {
            $messages[] = ['id' => $id, 'domain' => $domain, 'locale' => $locale, 'channelCode' => $channelCode];
        }

        return $messages;
    }

    /**
     * @return list<string>
     */
    private function getShopLocaleCodes(): array
    {
        if (null === $this->shopLocaleCodes) {
            $this->shopLocaleCodes = [];

            foreach (null === $this->localeRepository ? [] : $this->localeRepository->findAll() as $locale) {
                if (true === $locale instanceof LocaleInterface && null !== $locale->getCode()) {
                    $this->shopLocaleCodes[] = $locale->getCode();
                }
            }
        }

        return $this->shopLocaleCodes;
    }
}
