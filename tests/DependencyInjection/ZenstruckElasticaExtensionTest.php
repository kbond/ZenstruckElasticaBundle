<?php

namespace Zenstruck\ElasticaBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\Reference;
use Zenstruck\ElasticaBundle\DependencyInjection\ZenstruckElasticaExtension;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ZenstruckElasticaExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @test
     */
    public function register_just_client_and_index()
    {
        $this->load(array(
                'client' => array(),
                'index' => array('name' => 'foo'),
            ));
        $this->compile();

        $this->assertContainerBuilderHasService('zenstruck_elastica.client', 'Elastica\Client');
        $this->assertContainerBuilderHasService('zenstruck_elastica.index', 'Elastica\Index');
        $this->assertContainerBuilderHasService('zenstruck_elastica.index_context', 'Zenstruck\ElasticaBundle\Elastica\IndexContext');
        $this->assertContainerBuilderHasService('zenstruck_elastica.index_manager', 'Zenstruck\ElasticaBundle\Elastica\IndexManager');
        $this->assertNull($this->container->getParameter('zenstruck_elastica.index_settings'));

        $clientDefinition = $this->container->getDefinition('zenstruck_elastica.client');

        $this->assertFalse($clientDefinition->hasMethodCall('setLogger'));
        $this->assertFalse($clientDefinition->hasTag('monolog.logger'));
    }

    /**
     * @test
     */
    public function can_enable_logging()
    {
        $this->load(array(
                'logging' => true,
                'client' => array(),
                'index' => array('name' => 'foo'),
            ));
        $this->compile();

        $clientDefinition = $this->container->getDefinition('zenstruck_elastica.client');

        $this->assertTrue($clientDefinition->hasMethodCall('setLogger'));
        $this->assertTrue($clientDefinition->hasTag('monolog.logger'));
    }

    /**
     * @test
     */
    public function can_configure_index_settings()
    {
        $this->load(array(
                'client' => array(),
                'index' => array('name' => 'foo', 'settings' => array('foo')),
            ));
        $this->compile();

        $this->assertSame(array('foo'), $this->container->getParameter('zenstruck_elastica.index_settings'));
    }

    /**
     * @test
     */
    public function can_configure_types_with_mapping_array()
    {
        $this->load(array(
                'client' => array(),
                'index' => array('name' => 'foo'),
                'types' => array('bar' => array(
                    'service' => 'document_provider_service',
                    'mapping' => array('foo'),
                )),
            ));
        $this->compile();

        $this->assertContainerBuilderHasService('zenstruck_elastica.type.bar');
        $this->assertContainerBuilderHasService('zenstruck_elastica.type_context.bar');
        $this->assertContainerBuilderHasServiceDefinitionWithArgument('zenstruck_elastica.type_context.bar', 2, array('foo'));
    }

    /**
     * @test
     */
    public function can_configure_types_with_mapping_service()
    {
        $this->load(array(
            'client' => array(),
            'index' => array('name' => 'foo'),
            'types' => array('bar' => array(
                'service' => 'document_provider_service',
                'mapping' => 'mapping_provider_service',
            )),
        ));
        $this->compile();

        $this->assertContainerBuilderHasService('zenstruck_elastica.type.bar');
        $this->assertContainerBuilderHasService('zenstruck_elastica.type_context.bar');
        $this->assertContainerBuilderHasServiceDefinitionWithArgument('zenstruck_elastica.type_context.bar', 2, new Reference('mapping_provider_service'));
    }

    protected function getContainerExtensions()
    {
        return array(new ZenstruckElasticaExtension());
    }
}
