<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\Translation;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Symfony\Component\Translation\Formatter\IntlFormatterInterface;
use Symfony\Component\Translation\Formatter\MessageFormatterInterface;
use Symfony\Component\Translation\MessageCatalogueInterface;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class DatabaseTranslator implements TranslatorInterface, TranslatorBagInterface, LocaleAwareInterface
{
    public function __construct(
        private readonly TranslatorInterface&TranslatorBagInterface&LocaleAwareInterface $decorated,
        private readonly MessageProviderInterface                                        $messageProvider,
        private readonly ChannelContextInterface                                         $channelContext,
        private readonly MessageFormatterInterface                                       $messageFormatter,
    )
    {
    }

    public function trans(string $id, array $parameters = [], ?string $domain = null, ?string $locale = null)
    {
        $domain ??= 'messages';
        $locale ??= $this->getLocale();

        $intlMessage = false;

        try {

            // Résolu à chaque appel, sans mémoire : un worker (Messenger, commande) traite des
            // messages de canaux différents. Le contexte de canal de Sylius met déjà le canal en
            // cache le temps d'une requête HTTP.
            $channelCode = $this->channelContext->getChannel()->getCode();

            $message = $this->messageProvider->getMessage($id, $domain, $locale, $channelCode);

            if (null === $message && true === $this->messageFormatter instanceof IntlFormatterInterface) {
                $message = $this->messageProvider->getMessage($id, $domain . MessageCatalogueInterface::INTL_DOMAIN_SUFFIX, $locale, $channelCode);
                $intlMessage = true;
            }

        } catch (ChannelNotFoundException) {
            $message = null;
        }

        if (null === $message) {
            return $this->decorated->trans($id, $parameters, $domain, $locale);
        }

        if (true === $intlMessage) {
            return $this->messageFormatter->formatIntl($message, $locale, $parameters);
        }

        return $this->messageFormatter->format($message, $locale, $parameters);
    }

    public function setLocale(string $locale)
    {
        $this->decorated->setLocale($locale);
    }

    public function getCatalogue(?string $locale = null): MessageCatalogueInterface
    {
        return $this->decorated->getCatalogue($locale);
    }

    public function getCatalogues(): array
    {
        return $this->decorated->getCatalogues();
    }

    public function getLocale()
    {
        return $this->decorated->getLocale();
    }

    public function __call(string $method, array $arguments)
    {
        $arguments = array_values($arguments);

        return $this->decorated->$method(...$arguments);
    }
}
