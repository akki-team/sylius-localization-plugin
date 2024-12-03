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
            ->end();

        return $treeBuilder;
    }

}
