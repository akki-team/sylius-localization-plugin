<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\Tests\Unit\DependencyInjection;

use Akki\SyliusLocalizationPlugin\Cache\LocalizationCacheVersion;
use Akki\SyliusLocalizationPlugin\Cache\LocalizedEntryCacheClearer;
use Akki\SyliusLocalizationPlugin\Cache\LocalizedEntryCacheClearerInterface;
use Akki\SyliusLocalizationPlugin\DependencyInjection\AkkiSyliusLocalizationExtension;
use Akki\SyliusLocalizationPlugin\Translation\CacheMessageProvider;
use Akki\SyliusLocalizationPlugin\Translation\DatabaseTranslator;
use Akki\SyliusLocalizationPlugin\Translation\LocalCacheMessageProvider;
use Akki\SyliusLocalizationPlugin\Translation\MessageProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Charge la configuration des services du plugin (sans compiler le conteneur, faute des services
 * de Sylius) : détecte un format de configuration que la version de Symfony ne sait plus lire.
 */
final class AkkiSyliusLocalizationExtensionTest extends TestCase
{
    public function testDefaultConfiguration(): void
    {
        $container = $this->load([]);

        self::assertSame([MessageProvider::class, null, 256], $container->getDefinition(CacheMessageProvider::class)->getDecoratedService());
        self::assertSame([MessageProvider::class, null, 128], $container->getDefinition(LocalCacheMessageProvider::class)->getDecoratedService());
        self::assertFalse($container->getDefinition(LocalCacheMessageProvider::class)->getArgument(3), 'APCu est opt-in.');
        self::assertSame(LocalizedEntryCacheClearer::class, (string)$container->getAlias(LocalizedEntryCacheClearerInterface::class));
        self::assertSame('cache.app', (string)$container->getDefinition(LocalizationCacheVersion::class)->getArgument(0));

        foreach ([LocalCacheMessageProvider::class, LocalizationCacheVersion::class, LocalizedEntryCacheClearer::class, DatabaseTranslator::class] as $id) {
            self::assertTrue($container->getDefinition($id)->hasTag('kernel.reset'), $id . ' doit être remis à zéro entre deux messages.');
        }
    }

    public function testApcuAndCustomPool(): void
    {
        $container = $this->load([['cache' => 'localization.cache', 'local_cache' => ['apcu' => ['enabled' => true, 'ttl' => 60]]]]);
        $definition = $container->getDefinition(LocalCacheMessageProvider::class);

        self::assertTrue($definition->getArgument(3));
        self::assertSame(60, $definition->getArgument(4));
        self::assertMatchesRegularExpression('/^akki_localization\.[0-9a-f]{8}\.$/', $definition->getArgument(5));
        self::assertSame('localization.cache', (string)$container->getDefinition(CacheMessageProvider::class)->getArgument(2));
    }

    public function testLocalCacheCanBeDisabled(): void
    {
        self::assertFalse($this->load([['local_cache' => ['enabled' => false]]])->hasDefinition(LocalCacheMessageProvider::class));
    }

    private function load(array $configs): ContainerBuilder
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.project_dir', '/app');
        $container->setParameter('kernel.environment', 'prod');

        (new AkkiSyliusLocalizationExtension())->load($configs, $container);

        return $container;
    }
}
