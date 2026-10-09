<?php
declare(strict_types=1);


use Akki\SyliusLocalizationPlugin\Cache\LocalizedEntryCacheClearerInterface;
use Akki\SyliusLocalizationPlugin\EventListener\ClearLocalizedEntryCacheEventListener;
use Akki\SyliusLocalizationPlugin\Menu\MenuBuilderEventListener;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(MenuBuilderEventListener::class)
        ->tag('kernel.event_listener', ['event' => 'sylius.menu.admin.main']);

    $services->set(ClearLocalizedEntryCacheEventListener::class)
        ->args([service(LocalizedEntryCacheClearerInterface::class)])
        ->tag('kernel.event_listener', ['event' => 'akki_sylius_localization_plugin.localized_entry.post_create'])
        ->tag('kernel.event_listener', ['event' => 'akki_sylius_localization_plugin.localized_entry.post_update'])
        ->tag('kernel.event_listener', ['event' => 'akki_sylius_localization_plugin.localized_entry.post_delete']);
};
