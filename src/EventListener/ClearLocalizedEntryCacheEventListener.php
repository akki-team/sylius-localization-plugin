<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\EventListener;

use Akki\SyliusLocalizationPlugin\Cache\LocalizedEntryCacheClearerInterface;
use Akki\SyliusLocalizationPlugin\Entity\Localization\LocalizedEntryInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;

final class ClearLocalizedEntryCacheEventListener
{
    public function __construct(
        private readonly LocalizedEntryCacheClearerInterface $localizedEntryCacheClearer
    )
    {
    }

    public function __invoke(ResourceControllerEvent $event): void
    {
        $localizedEntry = $event->getSubject();
        
        if (false === $localizedEntry instanceof LocalizedEntryInterface) {
            return;
        }

        $this->localizedEntryCacheClearer->clear($localizedEntry);
    }
}
