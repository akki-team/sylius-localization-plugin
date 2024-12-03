<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Symfony\Component\Form\FormBuilderInterface;

final class LocalizedEntryType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('translations', ResourceTranslationsType::class, [
                'entry_type' => LocalizedEntryTranslationType::class,
            ]);
    }

    public function getBlockPrefix(): string
    {
        return 'akki_sylius_localization_plugin_localized_entry';
    }
}
