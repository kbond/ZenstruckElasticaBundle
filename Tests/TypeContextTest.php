<?php

namespace Zenstruck\ElasticaBundle\Tests;

use Mockery as m;
use Zenstruck\ElasticaBundle\TypeContext;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class TypeContextTest extends \PHPUnit_Framework_TestCase
{
    public function testGetType()
    {
        $context = new TypeContext(m::mock('Elastica\Type'), m::mock('Zenstruck\ElasticaBundle\DocumentProvider'), array('foo'));

        $this->assertInstanceOf('Elastica\Type', $context->getType());
    }

    public function testGetDocuments()
    {
        $provider = m::mock('Zenstruck\ElasticaBundle\DocumentProvider');
        $provider->shouldReceive('getDocuments')->andReturn(array('foo'));

        $context = new TypeContext(m::mock('Elastica\Type'), $provider, array('foo'));
        $this->assertSame(array('foo'), $context->getDocuments());
    }

    public function testGetMappingConfig()
    {
        $context = new TypeContext(
            m::mock('Elastica\Type'),
            m::mock('Zenstruck\ElasticaBundle\DocumentProvider'),
            array('foo')
        );

        $this->assertSame(array('foo'), $context->getMapping());
    }

    public function testGetMappingProvider()
    {
        $provider = m::mock('Zenstruck\ElasticaBundle\DocumentProvider', 'Zenstruck\ElasticaBundle\MappingProvider');
        $provider->shouldReceive('getMapping')->andReturn(array('foo'));

        $context = new TypeContext(m::mock('Elastica\Type'), $provider,  array('bar'));

        $this->assertSame(array('foo'), $context->getMapping());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNoMapping()
    {
        $context = new TypeContext(m::mock('Elastica\Type'), m::mock('Zenstruck\ElasticaBundle\DocumentProvider'));
    }
}
