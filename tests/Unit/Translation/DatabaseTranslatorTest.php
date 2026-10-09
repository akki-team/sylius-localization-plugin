<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\Tests\Unit\Translation;

use Akki\SyliusLocalizationPlugin\Translation\DatabaseTranslator;
use Akki\SyliusLocalizationPlugin\Translation\MessageProviderInterface;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Symfony\Component\Translation\Formatter\MessageFormatter;
use Symfony\Component\Translation\Translator;

final class DatabaseTranslatorTest extends TestCase
{
    public function testChannelIsResolvedOnceUntilReset(): void
    {
        $channels = [];

        foreach (['fls', 'dsn'] as $code) {
            $channel = $this->createMock(ChannelInterface::class);
            $channel->method('getCode')->willReturn($code);
            $channels[] = $channel;
        }

        $channelContext = $this->createMock(ChannelContextInterface::class);
        $channelContext->expects(self::exactly(2))->method('getChannel')->willReturnOnConsecutiveCalls(...$channels);

        $messageProvider = new class() implements MessageProviderInterface {
            public function getMessage(string $id, string $domain, string $locale, string $channelCode): ?string
            {
                return 'messages' === $domain ? 'Titre ' . $channelCode : null;
            }
        };

        $translator = new DatabaseTranslator(new Translator('fr_FR'), $messageProvider, $channelContext, new MessageFormatter());

        self::assertSame('Titre fls', $translator->trans('app.ui.title'));
        self::assertSame('Titre fls', $translator->trans('app.ui.subtitle'), 'Hors requête HTTP, le contexte de canal peut interroger la base : il est appelé une fois.');

        $translator->reset();

        self::assertSame('Titre dsn', $translator->trans('app.ui.title'), 'Entre deux messages Messenger, le canal est résolu à nouveau.');
    }
}
