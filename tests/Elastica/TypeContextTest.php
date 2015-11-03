<?php

namespace Zenstruck\ElasticaBundle\Tests\Elastica;

use Elastica\Document;
use Elastica\Type\Mapping;
use Zenstruck\ElasticaBundle\Elastica\TypeContext;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class TypeContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function can_get_type()
    {
        $context = new TypeContext(\Mockery::mock('Elastica\Type'), \Mockery::mock('Zenstruck\ElasticaBundle\Elastica\DocumentProvider'), array('foo'));

        $this->assertInstanceOf('Elastica\Type', $context->getType());
    }

    /**
     * @test
     */
    public function can_get_documents()
    {
        $documents = array(new Document());
        $provider = \Mockery::mock('Zenstruck\ElasticaBundle\Elastica\DocumentProvider');
        $provider->shouldReceive('getDocuments')->andReturn($documents);

        $context = new TypeContext(\Mockery::mock('Elastica\Type'), $provider, array('foo'));
        $this->assertSame($documents, $context->getDocuments());
    }

    /**
     * @test
     *
     * @dataProvider mappingProvider
     */
    public function can_get_mapping($mapping, $expectedResult)
    {
        $context = new TypeContext(
            \Mockery::mock('Elastica\Type'),
            \Mockery::mock('Zenstruck\ElasticaBundle\Elastica\DocumentProvider'),
            $mapping
        );

        $this->assertSame($expectedResult, $context->getMapping());
    }

    /**
     * @test
     *
     * @expectedException \InvalidArgumentException
     */
    public function fails_with_no_mapping()
    {
        $context = new TypeContext(\Mockery::mock('Elastica\Type'), \Mockery::mock('Zenstruck\ElasticaBundle\Elastica\DocumentProvider'), null);
    }

    public static function mappingProvider()
    {
        $mappingProvider = \Mockery::mock('Zenstruck\ElasticaBundle\Elastica\MappingProvider');
        $mappingProvider->shouldReceive('getMapping')->andReturn(array('foo'));

        $mapping = new Mapping();

        return array(
            array(array('foo'), array('foo')),
            array($mappingProvider, array('foo')),
            array($mapping, $mapping),
        );
    }
}
