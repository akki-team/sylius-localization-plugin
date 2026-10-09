<?php
declare(strict_types=1);


use Akki\SyliusLocalizationPlugin\Cache\Resolver\CacheKeyResolver;
use Akki\SyliusLocalizationPlugin\Cache\Resolver\CacheKeyResolverInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(CacheKeyResolver::class, CacheKeyResolver::class);
    $services->alias(CacheKeyResolverInterface::class, CacheKeyResolver::class);
};
