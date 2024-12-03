<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\Factory\Localization;

use Akki\SyliusLocalizationPlugin\Entity\Localization\LocalizedEntryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class LocalizedEntryFactory implements LocalizedEntryFactoryInterface
{
    public function __construct(
        private readonly FactoryInterface $localizedEntryFactory
    )
    {
    }

    public function createNew(): LocalizedEntryInterface
    {
        return $this->localizedEntryFactory->createNew();
    }

    public function createNewWithKeyAndDomain(ChannelInterface $channel, string $key, string $domain): LocalizedEntryInterface
    {
        $localizedEntry = $this->createNew();

        $localizedEntry->setChannel($channel);
        $localizedEntry->setKey($key);
        $localizedEntry->setDomain($domain);

        return $localizedEntry;
    }
}
