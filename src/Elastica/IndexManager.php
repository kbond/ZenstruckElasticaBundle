<?php

namespace Zenstruck\ElasticaBundle\Elastica;

use Elastica\Document;
use Elastica\Index;
use Elastica\Type;
use Zenstruck\ElasticaBundle\Exception\RuntimeException;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class IndexManager
{
    const DEFAULT_CHUNK_SIZE = 500;

    protected $indexContext;

    public function __construct(IndexContext $indexContext)
    {
        $this->indexContext = $indexContext;
    }

    /**
     * Creates and indexes the elasticsearch index.
     *
     * @throws RuntimeException
     */
    public function create()
    {
        $indicies = $this->indexContext->getIndicies();

        foreach ($indicies as $index) {
            if ($index->exists()) {
                throw RuntimeException::indexExists($index);
            }
        }

        /** @var Index $index */
        $index = reset($indicies);

        $this->doCreate($index);
        $index->addAlias($this->indexContext->getAlias()->getName(), true);
    }

    /**
     * Reindex the elasticsearch index.
     *
     * @throws RuntimeException
     */
    public function reindex()
    {
        $currentIndex = $this->getCurrentIndex();
        $freshIndex = $this->getFreshIndex();

        // create fresh index and add alias
        $this->doCreate($freshIndex);
        $freshIndex->addAlias($this->indexContext->getAlias()->getName(), true);

        // delete old index
        $currentIndex->delete();
    }

    /**
     * Deletes the elasticsearch index.
     *
     * @throws RuntimeException
     */
    public function delete()
    {
        foreach ($this->indexContext->getIndicies() as $index) {
            if ($index->exists()) {
                $index->delete();
            }
        }
    }

    private function doCreate(Index $index)
    {
        $indexContext = $this->indexContext;
        $typeContexts = $indexContext->getTypeContexts();
        $args = array();

        foreach ($typeContexts as $typeContext) {
            $args['mappings'][$typeContext->getType()->getName()]['properties'] = $typeContext->getMapping();
        }

        if (null !== $settings = $indexContext->getSettings()) {
            $args['settings'] = $settings;
        }

        $index->create($args);

        foreach ($typeContexts as $typeContext) {
            $type = new Type($index, $typeContext->getType()->getName());
            $this->addDocumentsToType($type, $typeContext->getDocuments());
        }
    }

    /**
     * @param Type       $type
     * @param Document[] $documents
     */
    private function addDocumentsToType(Type $type, array $documents)
    {
        foreach (array_chunk($documents, self::DEFAULT_CHUNK_SIZE) as $chunks) {
            $type->addDocuments($chunks);
        }
    }

    /**
     * @return Index
     */
    private function getFreshIndex()
    {
        foreach ($this->indexContext->getIndicies() as $index) {
            if (!$index->exists()) {
                return $index;
            }
        }

        throw new RuntimeException('No unused index in rotation. Run delete and create first.');
    }

    /**
     * @return Index
     */
    private function getCurrentIndex()
    {
        foreach ($this->indexContext->getIndicies() as $index) {
            if ($index->exists()) {
                return $index;
            }
        }

        throw new RuntimeException('No active index in rotation. Run create first.');
    }
}
