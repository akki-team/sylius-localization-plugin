<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\Tests\Unit\Cache;

use Akki\SyliusLocalizationPlugin\Cache\CacheKeySanitizer;
use Akki\SyliusLocalizationPlugin\Cache\LocalizationCacheInvalidator;
use Akki\SyliusLocalizationPlugin\Cache\LocalizationCacheVersion;
use Akki\SyliusLocalizationPlugin\Cache\LocalizedEntryCacheClearer;
use Akki\SyliusLocalizationPlugin\Cache\Resolver\CacheKeyResolver;
use Akki\SyliusLocalizationPlugin\Entity\Localization\LocalizedEntry;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Channel\Model\ChannelInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

final class LocalizedEntryCacheClearerTest extends TestCase
{
    private ArrayAdapter $pool;

    private LocalizationCacheVersion $version;

    private LocalizedEntryCacheClearer $clearer;

    protected function setUp(): void
    {
        $this->pool = new ArrayAdapter();
        $this->version = new LocalizationCacheVersion($this->pool);
        $this->clearer = new LocalizedEntryCacheClearer(new LocalizationCacheInvalidator(new CacheKeyResolver(), $this->pool, $this->version));
    }

    public function testEveryTranslationOfTheEntryIsCleared(): void
    {
        $entry = $this->createEntry('fls', 'app.ui.title', 'messages', ['fr_FR' => 'Titre', 'en_US' => 'Title']);
        $this->warm('fls.fr_FR.messages.app.ui.title', 'fls.en_US.messages.app.ui.title', 'dsn.fr_FR.messages.app.ui.title');
        $before = $this->version->current();

        $this->clearer->clear($entry);

        self::assertFalse($this->pool->hasItem('fls.fr_FR.messages.app.ui.title'));
        self::assertFalse($this->pool->hasItem('fls.en_US.messages.app.ui.title'));
        self::assertTrue($this->pool->hasItem('dsn.fr_FR.messages.app.ui.title'), 'Les autres canaux ne sont pas touchés.');
        self::assertNotSame($before, $this->version->current(), 'Le jeton des copies locales change.');
    }

    public function testKeyWithReservedCharactersIsClearedWithoutException(): void
    {
        $id = 'This value should be of type {{ type }}.';
        $entry = $this->createEntry('fls', $id, 'validators', ['fr_FR' => 'Cette valeur doit être de type {{ type }}.']);
        $stored = (new CacheKeySanitizer())->sanitize('fls.fr_FR.validators.' . $id);
        $this->warm($stored);

        $this->clearer->clear($entry);

        self::assertFalse($this->pool->hasItem($stored));
    }

    public function testEntryWithoutChannelOnlyChangesTheToken(): void
    {
        $entry = $this->createEntry(null, 'app.ui.title', 'messages', ['fr_FR' => 'Titre']);
        $this->warm('fls.fr_FR.messages.app.ui.title');
        $before = $this->version->current();

        $this->clearer->clear($entry);

        self::assertTrue($this->pool->hasItem('fls.fr_FR.messages.app.ui.title'));
        self::assertNotSame($before, $this->version->current());
    }

    /**
     * @param array<string, string> $values
     */
    private function createEntry(?string $channelCode, string $key, string $domain, array $values): LocalizedEntry
    {
        $entry = new LocalizedEntry();
        $entry->setKey($key);
        $entry->setDomain($domain);

        if (null !== $channelCode) {
            $channel = $this->createMock(ChannelInterface::class);
            $channel->method('getCode')->willReturn($channelCode);
            $entry->setChannel($channel);
        }

        foreach ($values as $locale => $value) {
            $entry->setCurrentLocale($locale);
            $entry->setFallbackLocale($locale);
            $entry->setValue($value, $locale);
        }

        return $entry;
    }

    private function warm(string ...$keys): void
    {
        foreach ($keys as $key) {
            $this->pool->get($key, static fn (): string => 'valeur en cache');
        }
    }
}
