<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\Tests\Unit\Cache;

use Akki\SyliusLocalizationPlugin\Cache\LocalizationCacheVersion;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\CacheItem;

final class LocalizationCacheVersionTest extends TestCase
{
    public function testTokenIsCreatedOnceAndSharedThroughThePool(): void
    {
        $pool = new ArrayAdapter();
        $version = new LocalizationCacheVersion($pool);

        $token = $version->current();

        self::assertNotNull($token);
        self::assertSame($token, $version->current());
        self::assertSame($token, (new LocalizationCacheVersion($pool))->current(), 'Un autre serveur lit le même jeton.');
    }

    public function testBumpChangesTheTokenForEveryServer(): void
    {
        $pool = new ArrayAdapter();
        $version = new LocalizationCacheVersion($pool);
        $other = new LocalizationCacheVersion($pool);
        $before = $version->current();
        $other->current();

        $version->bump();
        $other->reset();

        self::assertNotSame($before, $version->current());
        self::assertSame($version->current(), $other->current());
    }

    public function testClearedPoolGivesANewToken(): void
    {
        $pool = new ArrayAdapter();
        $version = new LocalizationCacheVersion($pool);
        $before = $version->current();

        $pool->clear();
        $version->reset();

        self::assertNotSame($before, $version->current());
    }

    public function testNoTokenWhenThePoolCannotKeepIt(): void
    {
        $pool = new class() extends ArrayAdapter {
            public int $reads = 0;

            public function getItem(mixed $key): CacheItem
            {
                ++$this->reads;

                return parent::getItem($key);
            }

            public function save(CacheItemInterface $item): bool
            {
                return false;
            }
        };
        $version = new LocalizationCacheVersion($pool);

        self::assertNull($version->current());
        self::assertNull($version->current());
        self::assertSame(1, $pool->reads, 'Pas de nouvel essai avant la requête suivante.');

        $version->reset();
        $version->current();
        self::assertSame(2, $pool->reads);
    }
}
