<?php

namespace Zenstruck\ElasticaBundle\Tests\Elastica;

use Mockery as m;
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
        $context = new IndexContext(m::mock('Elastica\Index'), array('foo'), array('bar'));

        $this->assertInstanceOf('Elastica\Index', $context->getIndex());
        $this->assertSame(array('foo'), $context->getTypeContexts());
        $this->assertSame(array('bar'), $context->getSettings());
    }
}
