<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\DependencyInjection;

use Akki\SyliusLocalizationPlugin\Cache\LocalizedEntryCacheClearer;
use Akki\SyliusLocalizationPlugin\Cache\LocalizedEntryCacheClearerInterface;
use Akki\SyliusLocalizationPlugin\Cache\Resolver\CacheKeyResolverInterface;
use Akki\SyliusLocalizationPlugin\Translation\CacheMessageProvider;
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


        $definition = new Definition(CacheMessageProvider::class, [
            new Reference('.inner'),
            new Reference(CacheKeyResolverInterface::class),
            new Reference($config['cache'])
        ]);

        $definition->setDecoratedService(MessageProvider::class, priority: 256);
        $container->setDefinition(CacheMessageProvider::class, $definition);


        $definition = new Definition(LocalizedEntryCacheClearer::class, [
            new Reference(CacheKeyResolverInterface::class),
            new Reference($config['cache'])
        ]);

        $container->setDefinition(LocalizedEntryCacheClearer::class, $definition);
        $container->setAlias(LocalizedEntryCacheClearerInterface::class, LocalizedEntryCacheClearer::class);
    }

}
