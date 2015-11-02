<?php

namespace Zenstruck\ElasticaBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ZenstruckElasticaExtension extends ConfigurableExtension
{
    /**
     * {@inheritdoc}
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $container->setParameter('zenstruck_elastica.client.config', $mergedConfig['client']);
        $container->setParameter('zenstruck_elastica.index.name', $mergedConfig['index']['name']);
        $container->setParameter('zenstruck_elastica.index_settings', $mergedConfig['index']['settings']);

        if ($mergedConfig['logging']) {
            $client = $container->getDefinition('zenstruck_elastica.client');
            $client->addMethodCall('setLogger', array(new Reference('logger')));
            $client->addTag('monolog.logger', array('channel' => 'elastica'));
        }

        $typeContexts = array();

        foreach ($mergedConfig['types'] as $alias => $config) {
            $type = new DefinitionDecorator('zenstruck_elastica.type');
            $type->addArgument($alias);
            $typeId = 'zenstruck_elastica.type.'.$alias;
            $container->setDefinition($typeId, $type);

            $typeContext = new DefinitionDecorator('zenstruck_elastica.type_context');
            $typeContext->setArguments(
                array(new Reference($typeId), new Reference($config['service']), $config['mapping'])
            );
            $typeContextId = 'zenstruck_elastica.type_context.'.$alias;
            $container->setDefinition($typeContextId, $typeContext);

            $typeContexts[$alias] = new Reference($typeContextId);
        }

        $container->getDefinition('zenstruck_elastica.index_context')
            ->replaceArgument(1, $typeContexts);
    }
}
