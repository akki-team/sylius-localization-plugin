<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\Tests\Unit\Cache;

use Akki\SyliusLocalizationPlugin\Cache\LocalizationCacheVersion;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\CacheItem;
use Symfony\Contracts\Cache\CacheInterface;

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

    public function testFailedBumpRemovesTheToken(): void
    {
        $pool = new ArrayAdapter();
        $version = new LocalizationCacheVersion($pool);
        $version->current();
        $failing = new class($pool) extends ArrayAdapter {
            public function __construct(private readonly ArrayAdapter $pool)
            {
                parent::__construct();
            }

            public function getItem(mixed $key): CacheItem
            {
                return $this->pool->getItem($key);
            }

            public function save(CacheItemInterface $item): bool
            {
                return false;
            }

            public function deleteItem(mixed $key): bool
            {
                return $this->pool->deleteItem($key);
            }
        };

        (new LocalizationCacheVersion($failing))->bump();

        self::assertFalse($pool->hasItem(LocalizationCacheVersion::CACHE_KEY), 'Les copies locales ne survivent pas à un jeton non renouvelé.');
    }

    public function testGenerationCountsInvalidationsOfTheProcess(): void
    {
        $version = new LocalizationCacheVersion(new ArrayAdapter());

        self::assertSame(0, $version->getGeneration());
        $version->bump();
        $version->reset();
        self::assertSame(1, $version->getGeneration(), 'kernel.reset ne remet pas le compteur à zéro.');
    }

    public function testCacheWithoutPsr6IsSupported(): void
    {
        $cache = new class() implements CacheInterface {
            private array $values = [];

            public function get(string $key, callable $callback, ?float $beta = null, ?array &$metadata = null): mixed
            {
                return $this->values[$key] ??= $callback(new CacheItem());
            }

            public function delete(string $key): bool
            {
                unset($this->values[$key]);

                return true;
            }
        };
        $version = new LocalizationCacheVersion($cache);
        $before = $version->current();

        self::assertNotNull($before);
        self::assertSame($before, (new LocalizationCacheVersion($cache))->current());

        $version->bump();
        self::assertNotSame($before, $version->current());
    }
}
