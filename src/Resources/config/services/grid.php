<?php
declare(strict_types=1);


use Akki\SyliusLocalizationPlugin\Grid\LocalizedEntryGrid;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(LocalizedEntryGrid::class, LocalizedEntryGrid::class)
        ->args([
            service('sylius.context.locale'),
            service('sylius.context.channel.composite'),
            service('akki_sylius_localization_plugin.repository.localized_entry'),
        ])
        ->tag('sylius.grid');
};
