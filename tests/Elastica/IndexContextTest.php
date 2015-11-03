<?php

namespace Zenstruck\ElasticaBundle\Tests\Elastica;

use Elastica\Index;
use Zenstruck\ElasticaBundle\Elastica\IndexContext;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class IndexContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function can_access_properties()
    {
        $client = \Mockery::mock('Elastica\Client');
        $alias = new Index($client, 'my_index');
        $context = new IndexContext($alias, array('foo'), array('bar'));

        $this->assertSame($alias, $context->getAlias());
        $this->assertSame(array('foo'), $context->getTypeContexts());
        $this->assertSame(array('bar'), $context->getSettings());
        $this->assertEquals(
            array(
                new Index($client, 'my_index1'),
                new Index($client, 'my_index2'),
            ),
            $context->getIndicies()
        );
    }
}
