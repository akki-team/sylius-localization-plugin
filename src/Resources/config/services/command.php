<?php
declare(strict_types=1);


use Akki\SyliusLocalizationPlugin\Cache\LocalizationCacheInvalidator;
use Akki\SyliusLocalizationPlugin\Command\LoadTranslationsCommand;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('akki.sylius_localization_plugin.command.load_translations_command', LoadTranslationsCommand::class)
        ->args([
            service('Sylius\Bundle\ThemeBundle\Translation\ThemeAwareTranslator'),
            service('sylius.repository.locale'),
            service('akki_sylius_localization_plugin.repository.localized_entry'),
            service('sylius.repository.channel'),
            service('akki_sylius_localization_plugin.factory.localized_entry'),
            service('doctrine.orm.entity_manager'),
            param('akki_sylius_localization_plugin.model.localized_entry.class'),
            service(LocalizationCacheInvalidator::class),
        ])
        ->tag('console.command');
};
