<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\Entity\Localization;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;

interface LocalizedEntryInterface extends ResourceInterface, TranslatableInterface, TimestampableInterface
{

}
