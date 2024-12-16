<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\Repository\Localization;

use Akki\SyliusLocalizationPlugin\Entity\Localization\LocalizedEntryInterface;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ChannelInterface;

class LocalizedEntryRepository extends EntityRepository implements LocalizedEntryRepositoryInterface
{
    public function createListQueryBuilder(string $localeCode): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->leftJoin('o.translations', 'translation', 'WITH', 'translation.locale = :localeCode')
            ->setParameter('localeCode', $localeCode);
    }


    public function findOneByKeyAndDomain(ChannelInterface $channel, string $key, string $domain): ?LocalizedEntryInterface
    {
        return $this->findOneBy(['channel' => $channel, 'key' => $key, 'domain' => $domain]);
    }

    public function getDomains(ChannelInterface $channel): array
    {
        return $this->createQueryBuilder('o')
            ->select('DISTINCT o.domain')
            ->where('o.channel = :channel')
            ->setParameter('channel', $channel)
            ->getQuery()
            ->getSingleColumnResult();
    }


}
