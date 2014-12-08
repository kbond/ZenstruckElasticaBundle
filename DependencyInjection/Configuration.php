<?php

namespace Zenstruck\ElasticaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('zenstruck_elastica');
        $rootNode
            ->children()
                ->booleanNode('logging')->defaultFalse()->end()
                ->variableNode('client')
                    ->isRequired()
                    ->validate()
                        ->ifTrue(function ($value) { return !is_array($value); })
                        ->thenInvalid('Client config must be an array.')
                    ->end()
                ->end()
                ->arrayNode('index')
                    ->children()
                        ->scalarNode('name')->isRequired()->end()
                        ->variableNode('settings')
                            ->defaultNull()
                            ->validate()
                                ->ifTrue(function ($value) { return !is_array($value); })
                                ->thenInvalid('Index settings must be an array.')
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('types')
                    ->useAttributeAsKey('alias')
                        ->prototype('array')
                            ->children()
                                ->scalarNode('service')->isRequired()->end()
                                ->variableNode('mapping')
                                ->defaultNull()
                                    ->validate()
                                        ->ifTrue(function ($value) { return !is_array($value); })
                                        ->thenInvalid('Type mapping must be an array.')
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
