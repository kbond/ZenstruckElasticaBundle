<?php

namespace Zenstruck\ElasticaBundle\Tests\Elastica;

use Elastica\Client;
use Elastica\Index;
use Elastica\Type;
use Zenstruck\ElasticaBundle\Elastica\IndexContext;
use Zenstruck\ElasticaBundle\Elastica\IndexManager;
use Zenstruck\ElasticaBundle\Elastica\TypeContext;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class IndexManagerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Index */
    private $alias;

    /** @var Index */
    private $index1;

    /** @var Index */
    private $index2;

    /**
     * @test
     */
    public function can_create_index()
    {
        $typeContext = new TypeContext(new Type($this->alias, 'foo'), \Mockery::mock('Zenstruck\ElasticaBundle\Elastica\DocumentProvider'), array(
            'title' => array('type' => 'string', 'analyzer' => 'stem'),
        ));

        $indexContext = new IndexContext($this->alias, array($typeContext), array(
            'analysis' => array(
                'analyzer' => array(
                    'stem' => array(
                        'tokenizer' => 'standard',
                        'filter' => array('standard', 'lowercase'),
                    ),
                ),
            ),
        ));

        $indexManager = new IndexManager($indexContext);

        $this->assertFalse($this->alias->exists());
        $this->assertFalse($this->index1->exists());
        $this->assertFalse($this->index2->exists());
        $indexManager->create();
        $this->assertTrue($this->alias->exists());
        $this->assertTrue($this->index1->exists());
        $this->assertFalse($this->index2->exists());

        $aliases = $this->alias->request('/_aliases', 'GET')->getData();
        $this->assertSame(array(), $aliases['zenstruck_elastica1']['aliases']['zenstruck_elastica']);

        $settings = $this->alias->request('/_settings', 'GET')->getData();
        $this->assertSame(array('standard', 'lowercase'), $settings['zenstruck_elastica1']['settings']['index']['analysis']['analyzer']['stem']['filter']);

        $mapping = $this->alias->request('/_mapping', 'GET')->getData();
        $this->assertSame('string', $mapping['zenstruck_elastica1']['mappings']['foo']['properties']['title']['type']);
        $this->assertSame('stem', $mapping['zenstruck_elastica1']['mappings']['foo']['properties']['title']['analyzer']);
    }

    /**
     * @test
     */
    public function can_delete_index()
    {
        $indexManager = new IndexManager(new IndexContext($this->alias, array()));

        $indexManager->create();
        $this->assertTrue($this->alias->exists());
        $this->assertTrue($this->index1->exists());
        $this->assertFalse($this->index2->exists());
        $indexManager->delete();
        $this->assertFalse($this->alias->exists());
        $this->assertFalse($this->index1->exists());
        $this->assertFalse($this->index2->exists());
    }

    /**
     * @test
     *
     * @expectedException \Zenstruck\ElasticaBundle\Exception\RuntimeException
     * @expectedExceptionMessage Index "zenstruck_elastica1" already exists.
     */
    public function cannot_create_index_that_already_exists()
    {
        $indexManager = new IndexManager(new IndexContext($this->alias, array()));
        $indexManager->create();
        $indexManager->create();
    }

    public function setUp()
    {
        $client = new Client(array('host' => 'localhost', 'port' => 9200));
        $this->alias = new Index($client, 'zenstruck_elastica');
        $this->index1 = new Index($client, 'zenstruck_elastica1');
        $this->index2 = new Index($client, 'zenstruck_elastica2');

        $this->tearDown();
    }

    protected function tearDown()
    {
        if ($this->alias->exists()) {
            $this->alias->delete();
        }

        if ($this->index1->exists()) {
            $this->index1->delete();
        }

        if ($this->index2->exists()) {
            $this->index2->delete();
        }
    }
}
