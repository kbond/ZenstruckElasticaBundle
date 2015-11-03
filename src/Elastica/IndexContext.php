<?php

namespace Zenstruck\ElasticaBundle\Elastica;

use Elastica\Index;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class IndexContext
{
    private $alias;
    private $typeContexts;
    private $settings;
    private $indicies;

    /**
     * @param Index         $alias
     * @param TypeContext[] $typeContexts
     * @param array         $settings
     */
    public function __construct(Index $alias, array $typeContexts, array $settings = null)
    {
        $this->alias = $alias;
        $this->typeContexts = $typeContexts;
        $this->settings = $settings;

        $client = $alias->getClient();

        $this->indicies = array(
            new Index($client, $alias->getName().'1'),
            new Index($client, $alias->getName().'2'),
        );
    }

    /**
     * @return Index
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @return TypeContext[]
     */
    public function getTypeContexts()
    {
        return $this->typeContexts;
    }

    /**
     * @return array|null
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @return Index[]
     */
    public function getIndicies()
    {
        return $this->indicies;
    }
}
