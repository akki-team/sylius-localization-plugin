<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\DependencyInjection;

use Akki\SyliusLocalizationPlugin\Cache\CacheKeySanitizer;
use Akki\SyliusLocalizationPlugin\Cache\LocalizationCacheInvalidator;
use Akki\SyliusLocalizationPlugin\Cache\LocalizationCacheVersion;
use Akki\SyliusLocalizationPlugin\Cache\LocalizedEntryCacheClearer;
use Akki\SyliusLocalizationPlugin\Cache\LocalizedEntryCacheClearerInterface;
use Akki\SyliusLocalizationPlugin\Cache\Resolver\CacheKeyResolverInterface;
use Akki\SyliusLocalizationPlugin\Translation\CacheMessageProvider;
use Akki\SyliusLocalizationPlugin\Translation\LocalCacheMessageProvider;
use Akki\SyliusLocalizationPlugin\Translation\MessageProvider;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

final class AkkiSyliusLocalizationExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $container->setDefinition(CacheKeySanitizer::class, new Definition(CacheKeySanitizer::class));

        $definition = new Definition(CacheMessageProvider::class, [
            new Reference('.inner'),
            new Reference(CacheKeyResolverInterface::class),
            new Reference($config['cache']),
            new Reference(CacheKeySanitizer::class),
        ]);

        $definition->setDecoratedService(MessageProvider::class, priority: 256);
        $container->setDefinition(CacheMessageProvider::class, $definition);


        $definition = new Definition(LocalizationCacheVersion::class, [
            new Reference($config['cache']),
        ]);

        $definition->addTag('kernel.reset', ['method' => 'reset']);
        $container->setDefinition(LocalizationCacheVersion::class, $definition);


        $container->setDefinition(LocalizationCacheInvalidator::class, new Definition(LocalizationCacheInvalidator::class, [
            new Reference(CacheKeyResolverInterface::class),
            new Reference($config['cache']),
            new Reference(LocalizationCacheVersion::class),
            new Reference(CacheKeySanitizer::class),
        ]));


        $definition = new Definition(LocalizedEntryCacheClearer::class, [
            new Reference(LocalizationCacheInvalidator::class),
            new Reference('sylius.repository.locale'),
        ]);

        $definition->addTag('kernel.reset', ['method' => 'reset']);
        $container->setDefinition(LocalizedEntryCacheClearer::class, $definition);
        $container->setAlias(LocalizedEntryCacheClearerInterface::class, LocalizedEntryCacheClearer::class);


        if (true === $config['local_cache']['enabled']) {
            $definition = new Definition(LocalCacheMessageProvider::class, [
                new Reference('.inner'),
                new Reference(CacheKeyResolverInterface::class),
                new Reference(LocalizationCacheVersion::class),
                $config['local_cache']['apcu']['enabled'],
                $config['local_cache']['apcu']['ttl'],
                // Plusieurs applications peuvent partager le même APCu (même pool PHP-FPM).
                sprintf('akki_localization.%s.', substr(hash('sha256', $container->getParameter('kernel.project_dir') . '|' . $container->getParameter('kernel.environment')), 0, 8)),
            ]);

            // Priorité inférieure à celle de CacheMessageProvider (256) : la copie locale est
            // consultée avant le cache partagé.
            $definition->setDecoratedService(MessageProvider::class, priority: 128);
            $definition->addTag('kernel.reset', ['method' => 'reset']);
            $container->setDefinition(LocalCacheMessageProvider::class, $definition);
        }
    }

}
