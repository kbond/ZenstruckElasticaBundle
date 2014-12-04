<?php

namespace Zenstruck\ElasticaBundle;

use Elastica\Index;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class IndexContext
{
    private $index;
    private $typeContexts;
    private $settings;

    /**
     * @param Index         $index
     * @param TypeContext[] $typeContexts
     * @param array         $settings
     */
    public function __construct(Index $index, array $typeContexts, array $settings = null)
    {
        $this->index = $index;
        $this->typeContexts = $typeContexts;
        $this->settings = $settings;
    }

    /**
     * @return Index
     */
    public function getIndex()
    {
        return $this->index;
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
}
