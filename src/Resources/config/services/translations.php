<?php
declare(strict_types=1);


use Akki\SyliusLocalizationPlugin\Translation\DatabaseTranslator;
use Akki\SyliusLocalizationPlugin\Translation\MessageProvider;
use Akki\SyliusLocalizationPlugin\Translation\MessageProviderInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(DatabaseTranslator::class)
        ->decorate('Sylius\Bundle\ThemeBundle\Translation\ThemeAwareTranslator', null, 128)
        ->args([
            service(DatabaseTranslator::class . '.inner'),
            service(MessageProvider::class),
            service('sylius.context.channel'),
            service('translator.formatter'),
        ])
        ->tag('kernel.reset', ['method' => 'reset']);

    $services->set(MessageProvider::class)
        ->args([
            service('akki_sylius_localization_plugin.repository.localized_entry'),
            service('sylius.repository.channel'),
        ]);

    $services->alias(MessageProviderInterface::class, MessageProvider::class);
};
