<?php

namespace Zenstruck\ElasticaBundle\Elastica;

use Elastica\Document;
use Elastica\Index;
use Elastica\Type;
use Psr\Log\LoggerInterface;
use Zenstruck\ElasticaBundle\Exception\RuntimeException;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class IndexManager
{
    const DEFAULT_CHUNK_SIZE = 500;

    private $indexContext;
    private $logger;

    public function __construct(IndexContext $indexContext, LoggerInterface $logger = null)
    {
        $this->indexContext = $indexContext;
        $this->logger = $logger;
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

        $this->logInfo(sprintf('Creating index "%s".', $index->getName()));
        $this->doCreate($index);

        $aliasName = $this->indexContext->getAlias()->getName();
        $this->logInfo(sprintf('Adding alias "%s" for index "%s".', $aliasName, $index->getName()));
        $index->addAlias($aliasName);
    }

    /**
     * Reindex the elasticsearch index.
     *
     * @throws RuntimeException
     */
    public function reindex()
    {
        $oldIndex = $this->getCurrentIndex();
        $newIndex = $this->getFreshIndex();

        $this->logInfo(sprintf('Creating new index "%s".', $newIndex->getName()));
        $this->doCreate($newIndex);

        $aliasName = $this->indexContext->getAlias()->getName();
        $this->logInfo(sprintf('Swapping alias "%s" from index "%s" to index "%s".', $aliasName, $oldIndex->getName(), $newIndex->getName()));
        $newIndex->addAlias($aliasName);

        $this->logInfo(sprintf('Deleting old index "%s".', $oldIndex->getName()));
        $oldIndex->delete();
    }

    /**
     * Deletes the elasticsearch index.
     *
     * @throws RuntimeException
     */
    public function delete()
    {
        foreach ($this->indexContext->getIndicies() as $index) {
            $this->doDelete($index);
        }
    }

    private function doDelete(Index $index)
    {
        if ($index->exists()) {
            $this->logInfo(sprintf('Deleting index "%s".', $index->getName()));
            $index->delete();
        }
    }

    /**
     * @param string $message
     * @param array  $context
     */
    private function logInfo($message, array $context = array())
    {
        if (null === $this->logger) {
            return;
        }

        $this->logger->info($message, $context);
    }

    private function doCreate(Index $index)
    {
        $args = array();

        if (null !== $settings = $this->indexContext->getSettings()) {
            $args['settings'] = $settings;
        }

        $index->create($args);

        foreach ($this->indexContext->getTypeContexts() as $typeContext) {
            $type = new Type($index, $typeContext->getType()->getName());

            $this->logInfo(sprintf('Adding mapping for type "%s" on index "%s".', $type->getName(), $index->getName()));
            $type->setMapping($typeContext->getMapping());
            $this->addDocumentsToType($type, $typeContext->getDocuments());
        }
    }

    /**
     * @param Type       $type
     * @param Document[] $documents
     */
    private function addDocumentsToType(Type $type, array $documents)
    {
        $total = count($documents);
        $typeName = $type->getName();
        $indexName = $type->getIndex()->getName();
        $count = 0;

        $this->logInfo(sprintf('Adding %d documents to type "%s" on index "%s".', $total, $typeName, $indexName));

        foreach (array_chunk($documents, self::DEFAULT_CHUNK_SIZE) as $chunks) {
            $type->addDocuments($chunks);

            $count += count($chunks);
            $this->logInfo(sprintf('%s/%s documents added to type "%s" on index "%s".',
                $count,
                $total,
                $typeName,
                $indexName
            ));
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
