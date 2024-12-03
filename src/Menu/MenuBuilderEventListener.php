<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\Menu;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class MenuBuilderEventListener
{
    public function __invoke(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

        $configurationMenu = $menu->getChild('configuration');

        if (null === $configurationMenu) {
            return;
        }

        $configurationMenu
            ->addChild('translations', options: [
                'route' => 'akki_sylius_localization_plugin_admin_localized_entry_index',
            ])
            ->setLabel('akki_sylius_localization_plugin.menu.admin.main.configuration.translations')
            ->setLabelAttribute('icon', 'translate');

    }
}
