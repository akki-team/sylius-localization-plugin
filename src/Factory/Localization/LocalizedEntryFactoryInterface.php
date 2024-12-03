<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\Factory\Localization;

use Akki\SyliusLocalizationPlugin\Entity\Localization\LocalizedEntryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface LocalizedEntryFactoryInterface extends FactoryInterface
{
    public function createNewWithKeyAndDomain(ChannelInterface $channel, string $key, string $domain): LocalizedEntryInterface;
}
