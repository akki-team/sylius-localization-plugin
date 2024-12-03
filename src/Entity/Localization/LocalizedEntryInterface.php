<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\Entity\Localization;

use Sylius\Component\Channel\Model\ChannelAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;

interface LocalizedEntryInterface extends ResourceInterface, TranslatableInterface, TimestampableInterface, ChannelAwareInterface
{
    public function getKey(): ?string;

    public function setKey(?string $key): static;

    public function getDomain(): ?string;

    public function setDomain(?string $domain): static;

    public function getValue(string $locale = null): ?string;

    public function setValue(?string $value, ?string $locale = null): static;
}
