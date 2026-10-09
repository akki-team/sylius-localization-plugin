<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('akki_sylius_localization_plugin');
        $root = $treeBuilder->getRootNode();

        $root
            ->children()
                ->scalarNode('cache')
                    ->defaultValue('cache.app')
                ->end()
                ->arrayNode('local_cache')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')
                            ->info('Mémoire des traductions le temps d\'une requête, devant le cache partagé.')
                            ->defaultTrue()
                        ->end()
                        ->arrayNode('apcu')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enabled')
                                    ->info('Copie APCu par serveur, invalidée par un jeton stocké dans le cache partagé. Ignorée si APCu est absent ou désactivé (CLI).')
                                    ->defaultFalse()
                                ->end()
                                ->integerNode('ttl')
                                    ->info('Durée de vie (s) des copies APCu : borne la fraîcheur des invalidations faites hors du plugin.')
                                    ->defaultValue(300)
                                    ->min(1)
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }

}
