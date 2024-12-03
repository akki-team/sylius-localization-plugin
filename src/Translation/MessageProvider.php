<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\Translation;

use Akki\SyliusLocalizationPlugin\Entity\Localization\LocalizedEntryInterface;
use Akki\SyliusLocalizationPlugin\Repository\Localization\LocalizedEntryRepositoryInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;

final class MessageProvider implements MessageProviderInterface
{
    public function __construct(
        private readonly LocalizedEntryRepositoryInterface $localizedEntryRepository,
        private readonly ChannelRepositoryInterface        $channelRepository,
    )
    {
    }

    public function getMessage(string $id, string $domain, string $locale, string $channelCode): ?string
    {
        $channel = $this->channelRepository->findOneByCode($channelCode);

        if (false === $channel instanceof ChannelInterface) {
            return null;
        }

        $localizedEntry = $this->localizedEntryRepository->findOneByKeyAndDomain($channel, $id, $domain);

        if (false === $localizedEntry instanceof LocalizedEntryInterface) {
            return null;
        }

        return $localizedEntry->getValue($locale);
    }
}
