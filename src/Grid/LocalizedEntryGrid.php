<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\Grid;

use Akki\SyliusLocalizationPlugin\Entity\Localization\LocalizedEntry;
use Akki\SyliusLocalizationPlugin\Repository\Localization\LocalizedEntryRepositoryInterface;
use Sylius\Bundle\GridBundle\Builder\Action\Action;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ActionGroupInterface;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Filter\SelectFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\StringFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;

final class LocalizedEntryGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public function __construct(
        private readonly LocaleContextInterface            $localeContext,
        private readonly ChannelContextInterface           $channelContext,
        private readonly LocalizedEntryRepositoryInterface $localizedEntryRepository,
    )
    {
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $domains = $this->localizedEntryRepository->getDomains($this->channelContext->getChannel());
        $choices = [];

        foreach ($domains as $domain) {
            $choices[$domain] = $domain;
        }

        $gridBuilder
            ->setDriver('doctrine/orm')
            ->setRepositoryMethod('createListQueryBuilder', [$this->localeContext->getLocaleCode()]);

        $gridBuilder
            ->addField(StringField::create('value')->setPath('value')->setLabel('akki_sylius_localization_plugin.ui.value')->setSortable(true))
            ->addField(StringField::create('key')->setLabel('akki_sylius_localization_plugin.ui.key')->setSortable(true))
            ->addField(StringField::create('domain')->setLabel('akki_sylius_localization_plugin.ui.domain')->setSortable(true));

        $gridBuilder
            ->addFilter(StringFilter::create('search')->setLabel('sylius.ui.search')->addOption('fields', ['key', 'translation.value']))
            ->addFilter(SelectFilter::create('domain', $choices)->setLabel('akki_sylius_localization_plugin.ui.domain'));

        $gridBuilder
            ->addAction(Action::create('update', 'update'), ActionGroupInterface::ITEM_GROUP)
            ->addAction(Action::create('delete', 'delete'), ActionGroupInterface::ITEM_GROUP);
    }

    public function getResourceClass(): string
    {
        return LocalizedEntry::class;
    }

    public static function getName(): string
    {
        return 'akki_sylius_localization_plugin.localized_entry';
    }
}
