<?php
declare(strict_types=1);


use Akki\SyliusLocalizationPlugin\Factory\Localization\LocalizedEntryFactory;
use Akki\SyliusLocalizationPlugin\Factory\Localization\LocalizedEntryFactoryInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('akki_sylius_localization_plugin.custom_factory.localized_entry', LocalizedEntryFactory::class)
        ->decorate('akki_sylius_localization_plugin.factory.localized_entry')
        ->args([service('akki_sylius_localization_plugin.custom_factory.localized_entry.inner')]);

    $services->alias(LocalizedEntryFactoryInterface::class, 'akki_sylius_localization_plugin.factory.localized_entry');
};
