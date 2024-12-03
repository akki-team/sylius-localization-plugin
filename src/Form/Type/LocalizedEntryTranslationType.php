<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

final class LocalizedEntryTranslationType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('value', TextareaType::class, [
                'label' => 'bitbag_sylius_cms_plugin.ui.name',
                'required' => true,

            ]);
    }

    public function getBlockPrefix(): string
    {
        return 'akki_sylius_localization_plugin_localized_entry_translation';
    }
}
