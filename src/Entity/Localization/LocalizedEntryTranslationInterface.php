<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\Entity\Localization;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TranslationInterface;

interface LocalizedEntryTranslationInterface extends ResourceInterface, TranslationInterface
{
    public function getValue(): ?string;

    public function setValue(?string $value): LocalizedEntryTranslation;
}
