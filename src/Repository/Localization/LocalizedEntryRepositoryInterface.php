<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\Repository\Localization;

use Akki\SyliusLocalizationPlugin\Entity\Localization\LocalizedEntryInterface;
use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface LocalizedEntryRepositoryInterface extends RepositoryInterface
{
    public function createListQueryBuilder(string $localeCode): QueryBuilder;

    public function findOneByKeyAndDomain(ChannelInterface $channel, string $key, string $domain): ?LocalizedEntryInterface;

    public function getDomains(ChannelInterface $channel): array;
}
