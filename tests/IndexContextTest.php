<?php

namespace Zenstruck\ElasticaBundle\Tests;

use Mockery as m;
use Zenstruck\ElasticaBundle\IndexContext;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class IndexContextTest extends \PHPUnit_Framework_TestCase
{
    public function testGetters()
    {
        $context = new IndexContext(m::mock('Elastica\Index'), array('foo'), array('bar'));

        $this->assertInstanceOf('Elastica\Index', $context->getIndex());
        $this->assertSame(array('foo'), $context->getTypeContexts());
        $this->assertSame(array('bar'), $context->getSettings());
    }
}
