<?php

namespace Zenstruck\ElasticaBundle\Elastica;

use Zenstruck\ElasticaBundle\Exception\RuntimeException;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class IndexManager
{
    private $indexContext;

    public function __construct(IndexContext $indexContext)
    {
        $this->indexContext = $indexContext;
    }

    /**
     * Creates the elasticsearch index.
     *
     * @throws RuntimeException
     */
    public function create()
    {
        $index = $this->indexContext->getIndex();

        if ($index->exists()) {
            throw RuntimeException::indexExists($index);
        }

        $args = array();

        foreach ($this->indexContext->getTypeContexts() as $typeContext) {
            $args['mappings'][$typeContext->getType()->getName()]['properties'] = $typeContext->getMapping();
        }

        if (null !== $settings = $this->indexContext->getSettings()) {
            $args['settings'] = $settings;
        }

        $index->create($args);
    }

    /**
     * Deletes the elasticsearch index.
     *
     * @throws RuntimeException
     */
    public function delete()
    {
        $index = $this->indexContext->getIndex();

        if (!$index->exists()) {
            throw RuntimeException::indexNotExists($index);
        }

        $index->delete();
    }
}
