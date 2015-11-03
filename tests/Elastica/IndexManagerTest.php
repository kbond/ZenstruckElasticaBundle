<?php

namespace Zenstruck\ElasticaBundle\Tests\Elastica;

use Elastica\Client;
use Elastica\Document;
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
    const TYPE_NAME = 'foo';

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
        $this->assertFalse($this->alias->exists());
        $this->assertFalse($this->index1->exists());
        $this->assertFalse($this->index2->exists());

        $this->createIndexManager()->create();
        $this->assertTrue($this->alias->exists());
        $this->assertTrue($this->index1->exists());
        $this->assertFalse($this->index2->exists());

        $aliases = $this->alias->request('/_aliases', 'GET')->getData();
        $this->assertSame(array(), $aliases['zenstruck_elastica1']['aliases']['zenstruck_elastica']);

        $settings = $this->alias->request('/_settings', 'GET')->getData();
        $this->assertSame(array('standard', 'lowercase'), $settings['zenstruck_elastica1']['settings']['index']['analysis']['analyzer']['stem']['filter']);

        $mapping = $this->alias->request('/_mapping', 'GET')->getData();
        $this->assertSame('string', $mapping['zenstruck_elastica1']['mappings'][self::TYPE_NAME]['properties']['title']['type']);
        $this->assertSame('stem', $mapping['zenstruck_elastica1']['mappings'][self::TYPE_NAME]['properties']['title']['analyzer']);
        $this->assertSame('my document title', $this->alias->getType(self::TYPE_NAME)->getDocument(1)->get('title'));
    }

    /**
     * @test
     */
    public function can_reindex()
    {
        $this->can_create_index();

        $this->createIndexManager()->reindex();
        $this->assertTrue($this->alias->exists());
        $this->assertFalse($this->index1->exists());
        $this->assertTrue($this->index2->exists());

        $aliases = $this->alias->request('/_aliases', 'GET')->getData();
        $this->assertSame(array(), $aliases['zenstruck_elastica2']['aliases']['zenstruck_elastica']);

        $settings = $this->alias->request('/_settings', 'GET')->getData();
        $this->assertSame(array('standard', 'lowercase'), $settings['zenstruck_elastica2']['settings']['index']['analysis']['analyzer']['stem']['filter']);

        $mapping = $this->alias->request('/_mapping', 'GET')->getData();
        $this->assertSame('string', $mapping['zenstruck_elastica2']['mappings'][self::TYPE_NAME]['properties']['title']['type']);
        $this->assertSame('stem', $mapping['zenstruck_elastica2']['mappings'][self::TYPE_NAME]['properties']['title']['analyzer']);
        $this->assertSame('my document title', $this->alias->getType(self::TYPE_NAME)->getDocument(1)->get('title'));
    }

    /**
     * @test
     */
    public function can_delete_index()
    {
        $indexManager = $this->createIndexManager();

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
        $indexManager = $this->createIndexManager();
        $indexManager->create();
        $indexManager->create();
    }

    /**
     * @test
     *
     * @expectedException \Zenstruck\ElasticaBundle\Exception\RuntimeException
     * @expectedExceptionMessage No unused index in rotation. Run delete and create first.
     */
    public function cannot_reindex_with_no_fresh_index()
    {
        $this->index1->create();
        $this->index2->create();
        $this->createIndexManager()->reindex();
    }

    /**
     * @test
     *
     * @expectedException \Zenstruck\ElasticaBundle\Exception\RuntimeException
     * @expectedExceptionMessage No active index in rotation. Run create first.
     */
    public function cannot_reindex_with_no_active_index()
    {
        $this->createIndexManager()->reindex();
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

    private function createIndexManager()
    {
        $documentProvider = \Mockery::mock('Zenstruck\ElasticaBundle\Elastica\DocumentProvider');
        $documentProvider->shouldReceive('getDocuments')
            ->andReturn(array(new Document(1, array('title' => 'my document title'))));

        $typeContext = new TypeContext(new Type($this->alias, self::TYPE_NAME), $documentProvider, array(
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

        return new IndexManager($indexContext);
    }
}
