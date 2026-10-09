<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\Command;

use Akki\SyliusLocalizationPlugin\Cache\LocalizationCacheInvalidator;
use Akki\SyliusLocalizationPlugin\Entity\Localization\LocalizedEntryInterface;
use Akki\SyliusLocalizationPlugin\Entity\Localization\LocalizedEntryTranslationInterface;
use Akki\SyliusLocalizationPlugin\Factory\Localization\LocalizedEntryFactoryInterface;
use Akki\SyliusLocalizationPlugin\Repository\Localization\LocalizedEntryRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Translation\MessageCatalogueInterface;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class LoadTranslationsCommand extends Command
{
    /** @var list<array{id: string, domain: string, locale: string, channelCode: string}> */
    private array $messagesToInvalidate = [];

    public function __construct(
        private readonly TranslatorInterface&TranslatorBagInterface $translator,
        private readonly RepositoryInterface                        $localeRepository,
        private readonly LocalizedEntryRepositoryInterface          $localizedEntryRepository,
        private readonly ChannelRepositoryInterface                 $channelRepository,
        private readonly LocalizedEntryFactoryInterface             $localizedEntryFactory,
        private readonly EntityManagerInterface                     $entityManager,
        private readonly string                                     $localizedEntryClass,
        private readonly ?LocalizationCacheInvalidator              $localizationCacheInvalidator = null,
    )
    {
        parent::__construct();
    }

    public function configure(): void
    {
        $this
            ->setName('akki:translations:load')
            ->addOption('force', 'f', null, 'Force update');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $io = new SymfonyStyle($input, $output);

        $force = $input->getOption('force');

        if (true === $force) {
            $response = $io->confirm('All translations will be erase. Are you sure you want to force update ?', false);

            if (false === $response) {
                return Command::SUCCESS;
            }
        }

        $this->messagesToInvalidate = [];

        if (true === $force) {
            $io->title('Clearing translations from database.');
            // Le DELETE DQL ne passe pas par le vidage du cache : on garde la liste pour vider
            // ensuite les traductions effacées (dont les valeurs saisies en BO).
            $this->messagesToInvalidate = $this->findAllMessages();
            $this->entityManager->createQuery(sprintf('DELETE FROM %s e', $this->localizedEntryClass))->execute();
            $io->success('Done');
        }

        $io->title('Initializing translations from locales.');

        $locales = $this->localeRepository->findAll();

        foreach ($locales as $locale) {
            if (false === $locale instanceof LocaleInterface) {
                continue;
            }

            $this->translator->getCatalogue($locale->getCode());
        }

        $io->success('Done');

        $io->title('Import translations in database.');

        foreach ($this->channelRepository->findAll() as $channel) {

            $io->section('Channel: ' . $channel->getName());

            if (false === $channel instanceof ChannelInterface) {
                continue;
            }

            foreach ($locales as $locale) {

                $io->text('Locale: ' . $locale->getCode());

                if (false === $locale instanceof LocaleInterface) {
                    continue;
                }

                $domains = $this->translator->getCatalogue($locale->getCode())->getDomains();

                foreach ($this->translator->getCatalogues() as $catalogue) {
                    $domains = array_merge($domains, $catalogue->getDomains());
                    $domains = array_merge($domains, $catalogue->getFallbackCatalogue()->getDomains());
                }

                $domains = array_unique($domains);

                foreach ($domains as $domain) {
                    $messages = $this->translator->getCatalogue($locale->getCode())->all($domain);
                    $this->importMessages($messages, $domain, $channel, $locale);

                    $messages = $this->translator->getCatalogue($locale->getCode())->getFallbackCatalogue()->all($domain);
                    $this->importMessages($messages, $domain, $channel, $locale);
                }
                $io->success('Done');
            }
        }

        if (null !== $this->localizationCacheInvalidator && [] !== $this->messagesToInvalidate) {
            $io->title('Clearing imported translations from cache.');
            $this->localizationCacheInvalidator->invalidate($this->messagesToInvalidate);
            $io->success(sprintf('%d translations cleared.', \count($this->messagesToInvalidate)));
        }

        $io->success('Import is now completed.');

        return Command::SUCCESS;
    }

    /**
     * @return list<array{id: string, domain: string, locale: string, channelCode: string}>
     */
    private function findAllMessages(): array
    {
        return $this->entityManager
            ->createQuery(sprintf('SELECT e.key AS id, e.domain AS domain, t.locale AS locale, c.code AS channelCode FROM %s e JOIN e.channel c JOIN e.translations t', $this->localizedEntryClass))
            ->getArrayResult();
    }

    private function importMessages(array $messages, string $defaultDomain, ChannelInterface $channel, LocaleInterface $locale): void
    {
        foreach ($messages as $key => $message) {
            $domain = $this->getDomain($key, $locale->getCode(), $defaultDomain);
            $channel = $this->channelRepository->find($channel->getId());

            if (false === $channel instanceof ChannelInterface) {
                continue;
            }

            $locale = $this->localeRepository->find($locale->getId());

            if (false === $locale instanceof LocaleInterface) {
                continue;
            }

            $localizedEntry = $this->localizedEntryRepository->findOneByKeyAndDomain($channel, $key, $domain);

            if (false === $localizedEntry instanceof LocalizedEntryInterface) {
                $localizedEntry = $this->localizedEntryFactory->createNewWithKeyAndDomain($channel, $key, $domain);
                $this->entityManager->persist($localizedEntry);
            }

            $localizedEntryTranslation = $localizedEntry->getTranslation($locale->getCode());

            if (false === $localizedEntryTranslation instanceof LocalizedEntryTranslationInterface) {
                throw new \Exception('Translation not found');
            }

            if (null !== $localizedEntryTranslation->getValue()) {
                continue;
            }

            $message = preg_replace('/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}\x{1F900}-\x{1F9FF}]/u', '', (string)$message);

            $localizedEntryTranslation->setValue($message);

            // Une absence a pu être mise en cache avant l'import : on la vide à la fin.
            $this->messagesToInvalidate[] = ['id' => (string)$key, 'domain' => $domain, 'locale' => $locale->getCode(), 'channelCode' => $channel->getCode()];

            $this->entityManager->flush();
            $this->entityManager->clear();
        }
    }

    private function getDomain(string $key, string $locale, string $domain): string
    {
        $intlDomain = $domain . MessageCatalogueInterface::INTL_DOMAIN_SUFFIX;

        if (true === $this->translator->getCatalogue($locale)->defines($key, $intlDomain)) {
            return $intlDomain;
        }

        return $domain;
    }
}
