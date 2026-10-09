<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\Tests\Unit\Translation;

use Akki\SyliusLocalizationPlugin\Cache\LocalizationCacheVersion;
use Akki\SyliusLocalizationPlugin\Cache\Resolver\CacheKeyResolver;
use Akki\SyliusLocalizationPlugin\Translation\LocalCacheMessageProvider;
use Akki\SyliusLocalizationPlugin\Translation\MessageProviderInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

final class LocalCacheMessageProviderTest extends TestCase
{
    public function testSameMessageIsReadOnceDuringARequest(): void
    {
        $inner = $this->createInner(['app.ui.title' => 'Titre']);
        $provider = new LocalCacheMessageProvider($inner, new CacheKeyResolver(), new LocalizationCacheVersion(new ArrayAdapter()));

        self::assertSame('Titre', $provider->getMessage('app.ui.title', 'messages', 'fr_FR@fleurus', 'fls'));
        self::assertSame('Titre', $provider->getMessage('app.ui.title', 'messages', 'fr_FR', 'fls'));
        self::assertSame(1, $inner->calls);
    }

    public function testMissingMessageIsAlsoRemembered(): void
    {
        $inner = $this->createInner([]);
        $provider = new LocalCacheMessageProvider($inner, new CacheKeyResolver(), new LocalizationCacheVersion(new ArrayAdapter()));

        self::assertNull($provider->getMessage('fls.app.ui.title', 'messages', 'fr_FR', 'fls'));
        self::assertNull($provider->getMessage('fls.app.ui.title', 'messages', 'fr_FR', 'fls'));
        self::assertSame(1, $inner->calls);
    }

    public function testChannelsAreNotMixed(): void
    {
        $inner = $this->createInner(['app.ui.title' => 'Titre']);
        $provider = new LocalCacheMessageProvider($inner, new CacheKeyResolver(), new LocalizationCacheVersion(new ArrayAdapter()));

        $provider->getMessage('app.ui.title', 'messages', 'fr_FR', 'fls');
        $provider->getMessage('app.ui.title', 'messages', 'fr_FR', 'dsn');

        self::assertSame(2, $inner->calls);
    }

    public function testResetEmptiesTheRequestMemoryWhenApcuIsDisabled(): void
    {
        $inner = $this->createInner(['app.ui.title' => 'Titre']);
        $provider = new LocalCacheMessageProvider($inner, new CacheKeyResolver(), new LocalizationCacheVersion(new ArrayAdapter()));

        $provider->getMessage('app.ui.title', 'messages', 'fr_FR', 'fls');
        $provider->reset();
        $provider->getMessage('app.ui.title', 'messages', 'fr_FR', 'fls');

        self::assertSame(2, $inner->calls);
    }

    public function testApcuCopyIsSharedUntilTheTokenChanges(): void
    {
        if (false === \function_exists('apcu_enabled') || false === apcu_enabled()) {
            self::markTestSkipped('APCu indisponible (en CLI : apc.enable_cli=1).');
        }

        $pool = new ArrayAdapter();
        $version = new LocalizationCacheVersion($pool);
        $inner = $this->createInner(['app.ui.title' => 'Titre']);
        $prefix = 'akki_localization_test.' . bin2hex(random_bytes(4)) . '.';
        $provider = new LocalCacheMessageProvider($inner, new CacheKeyResolver(), $version, true, 60, $prefix);

        $provider->getMessage('app.ui.title', 'messages', 'fr_FR', 'fls');
        $provider->reset();
        $other = new LocalCacheMessageProvider($inner, new CacheKeyResolver(), new LocalizationCacheVersion($pool), true, 60, $prefix);
        $other->getMessage('app.ui.title', 'messages', 'fr_FR', 'fls');

        self::assertSame(1, $inner->calls, 'Lu depuis APCu après le vidage de la mémoire de requête.');

        $version->bump();
        $provider->reset();
        $provider->getMessage('app.ui.title', 'messages', 'fr_FR', 'fls');

        self::assertSame(2, $inner->calls, 'Nouveau jeton : la copie APCu est ignorée.');
    }

    /**
     * @param array<string, string> $messages
     */
    private function createInner(array $messages): MessageProviderInterface
    {
        return new class($messages) implements MessageProviderInterface {
            public int $calls = 0;

            public function __construct(private readonly array $messages)
            {
            }

            public function getMessage(string $id, string $domain, string $locale, string $channelCode): ?string
            {
                ++$this->calls;

                return $this->messages[$id] ?? null;
            }
        };
    }
}
