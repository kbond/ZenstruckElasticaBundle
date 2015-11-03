<?php

namespace Zenstruck\ElasticaBundle\Elastica;

use Elastica\Index;
use Zenstruck\ElasticaBundle\Exception\RuntimeException;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class IndexManager
{
    protected $indexContext;

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
        $indicies = $this->indexContext->getIndicies();

        foreach ($indicies as $index) {
            if ($index->exists()) {
                throw RuntimeException::indexExists($index);
            }
        }

        /** @var Index $index */
        $index = reset($indicies);
        $args = array();

        foreach ($this->indexContext->getTypeContexts() as $typeContext) {
            $args['mappings'][$typeContext->getType()->getName()]['properties'] = $typeContext->getMapping();
        }

        if (null !== $settings = $this->indexContext->getSettings()) {
            $args['settings'] = $settings;
        }

        $index->create($args);
        $index->addAlias($this->indexContext->getAlias()->getName(), true);
    }

    /**
     * Deletes the elasticsearch indices.
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
}
