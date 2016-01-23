<?php

namespace CyberApp\Select2Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('cyber_app_select2')
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('configs')
                    ->prototype('variable')
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
