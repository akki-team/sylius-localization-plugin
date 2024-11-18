<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class LoadTranslationsCommand extends Command
{
    public function __construct(
        private readonly TranslatorInterface&TranslatorBagInterface $translator
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
        $catalogues = $this->translator->getCatalogues();

        foreach ($catalogues as $catalogue) {
            foreach ($catalogue->getDomains() as $domain) {
                dump($domain);
                exit;
            }
        }


        return Command::SUCCESS;
    }
}
