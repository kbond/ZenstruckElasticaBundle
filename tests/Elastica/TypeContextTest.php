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
        $context = new TypeContext(
            $this->getMock('Elastica\Type', array(), array(), '', false),
            $this->getMock('Zenstruck\ElasticaBundle\Elastica\DocumentProvider'),
            array('foo'));

        $this->assertInstanceOf('Elastica\Type', $context->getType());
    }

    /**
     * @test
     */
    public function can_get_documents()
    {
        $documents = array(new Document());
        $provider = $this->getMock('Zenstruck\ElasticaBundle\Elastica\DocumentProvider');
        $provider->expects($this->once())
            ->method('getDocuments')
            ->willReturn($documents);

        $context = new TypeContext($this->getMock('Elastica\Type', array(), array(), '', false), $provider, array('foo'));
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
            $this->getMock('Elastica\Type', array(), array(), '', false),
            $this->getMock('Zenstruck\ElasticaBundle\Elastica\DocumentProvider'),
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
        $context = new TypeContext(
            $this->getMock('Elastica\Type', array(), array(), '', false),
            $this->getMock('Zenstruck\ElasticaBundle\Elastica\DocumentProvider'), null);
    }

    public function mappingProvider()
    {
        $mappingProvider = $this->getMock('Zenstruck\ElasticaBundle\Elastica\MappingProvider');
        $mappingProvider->expects($this->once())
            ->method('getMapping')
            ->willReturn(array('foo'));

        $mapping = new Mapping();

        return array(
            array(array('foo'), array('foo')),
            array($mappingProvider, array('foo')),
            array($mapping, $mapping),
        );
    }
}
