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
    /** @var Client */
    private $client;

    /** @var Index */
    private $index;

    /**
     * @test
     */
    public function can_create_index()
    {
        $typeContext = new TypeContext(new Type($this->index, 'foo'), \Mockery::mock('Zenstruck\ElasticaBundle\Elastica\DocumentProvider'), array(
            'title' => array('type' => 'string', 'analyzer' => 'stem'),
        ));

        $indexContext = new IndexContext($this->index, array($typeContext), array(
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

        $this->assertFalse($this->index->exists());
        $indexManager->create();
        $this->assertTrue($this->index->exists());

        $settings = $this->index->request('/_settings', 'GET')->getData();
        $this->assertSame(array('standard', 'lowercase'), $settings['zenstruck_elastica']['settings']['index']['analysis']['analyzer']['stem']['filter']);

        $mapping = $this->index->request('/_mapping', 'GET')->getData();
        $this->assertSame('string', $mapping['zenstruck_elastica']['mappings']['foo']['properties']['title']['type']);
        $this->assertSame('stem', $mapping['zenstruck_elastica']['mappings']['foo']['properties']['title']['analyzer']);
    }

    /**
     * @test
     */
    public function can_delete_index()
    {
        $indexManager = new IndexManager(new IndexContext($this->index, array()));

        $indexManager->create();
        $this->assertTrue($this->index->exists());
        $indexManager->delete();
        $this->assertFalse($this->index->exists());
    }

    /**
     * @test
     *
     * @expectedException \Zenstruck\ElasticaBundle\Exception\RuntimeException
     * @expectedExceptionMessage Index "zenstruck_elastica" does not exist.
     */
    public function cannot_delete_non_existant_index()
    {
        $indexManager = new IndexManager(new IndexContext($this->index, array()));
        $indexManager->delete();
    }

    /**
     * @test
     *
     * @expectedException \Zenstruck\ElasticaBundle\Exception\RuntimeException
     * @expectedExceptionMessage Index "zenstruck_elastica" already exists.
     */
    public function cannot_create_index_that_already_exists()
    {
        $indexManager = new IndexManager(new IndexContext($this->index, array()));
        $indexManager->create();
        $indexManager->create();
    }

    public function setUp()
    {
        $this->client = new Client(array('host' => 'localhost', 'port' => 9200));
        $this->index = new Index($this->client, 'zenstruck_elastica');

        if ($this->index->exists()) {
            $this->index->delete();
        }
    }
}
