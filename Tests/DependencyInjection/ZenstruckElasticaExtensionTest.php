<?php

namespace Zenstruck\ElasticaBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Zenstruck\ElasticaBundle\DependencyInjection\ZenstruckElasticaExtension;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ZenstruckElasticaExtensionTest extends AbstractExtensionTestCase
{
    public function testJustClientAndIndex()
    {
        $this->load(array(
                'client' => array(),
                'index' => array('name' => 'foo'),
            ));
        $this->compile();

        $this->assertContainerBuilderHasService('zenstruck_elastica.client');
        $this->assertContainerBuilderHasService('zenstruck_elastica.index');
        $this->assertContainerBuilderHasService('zenstruck_elastica.index_context');
        $this->assertNull($this->container->getParameter('zenstruck_elastica.index_settings'));

        $clientDefinition = $this->container->getDefinition('zenstruck_elastica.client');

        $this->assertFalse($clientDefinition->hasMethodCall('setLogger'));
        $this->assertFalse($clientDefinition->hasTag('monolog.logger'));
    }

    public function testClientWithLogging()
    {
        $this->load(array(
                'logging' => true,
                'client' => array(),
                'index' => array('name' => 'foo'),
            ));
        $this->compile();

        $this->assertContainerBuilderHasService('zenstruck_elastica.client');

        $clientDefinition = $this->container->getDefinition('zenstruck_elastica.client');

        $this->assertTrue($clientDefinition->hasMethodCall('setLogger'));
        $this->assertTrue($clientDefinition->hasTag('monolog.logger'));
    }

    public function testIndexSettings()
    {
        $this->load(array(
                'client' => array(),
                'index' => array('name' => 'foo', 'settings' => array('foo')),
            ));
        $this->compile();

        $this->assertSame(array('foo'), $this->container->getParameter('zenstruck_elastica.index_settings'));
    }

    public function testTypes()
    {
        $this->load(array(
                'client' => array(),
                'index' => array('name' => 'foo'),
                'types' => array(
                    'bar' => array('service' => 'bar_service'),
                ),
            ));
        $this->compile();

        $this->assertContainerBuilderHasService('zenstruck_elastica.type.bar');
        $this->assertContainerBuilderHasService('zenstruck_elastica.type_context.bar');
    }

    protected function getContainerExtensions()
    {
        return array(new ZenstruckElasticaExtension());
    }
}
