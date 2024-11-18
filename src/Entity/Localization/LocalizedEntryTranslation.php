<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\Entity\Localization;

use Sylius\Component\Resource\Model\AbstractTranslation;

class LocalizedEntryTranslation extends AbstractTranslation implements LocalizedEntryTranslationInterface
{
    protected ?int $id = null;

    protected ?string $value = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): LocalizedEntryTranslation
    {
        $this->value = $value;
        return $this;
    }

}
