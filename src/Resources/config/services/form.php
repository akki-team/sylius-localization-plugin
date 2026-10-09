<?php
declare(strict_types=1);


use Akki\SyliusLocalizationPlugin\Form\Type\LocalizedEntryTranslationType;
use Akki\SyliusLocalizationPlugin\Form\Type\LocalizedEntryType;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('akki_sylius_localization_plugin.form.localized_entry_type', LocalizedEntryType::class)
        ->args([
            param('akki_sylius_localization_plugin.model.localized_entry.class'),
            param('akki_sylius_localization_plugin.model.localized_entry.validation_groups'),
        ])
        ->tag('form.type');

    $services->set('akki_sylius_localization_plugin.form.localized_entry_translation_type', LocalizedEntryTranslationType::class)
        ->args([
            param('akki_sylius_localization_plugin.model.localized_entry_translation.class'),
            param('akki_sylius_localization_plugin.model.localized_entry_translation.validation_groups'),
        ])
        ->tag('form.type');
};
