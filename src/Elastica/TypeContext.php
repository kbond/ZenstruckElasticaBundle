<?php

namespace Zenstruck\ElasticaBundle\Elastica;

use Elastica\Document;
use Elastica\Type;
use Elastica\Type\Mapping;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class TypeContext
{
    private $type;
    private $documentProvider;
    private $mapping;

    /**
     * @param Type                  $type
     * @param DocumentProvider      $documentProvider
     * @param MappingProvider|array $mapping
     */
    public function __construct(Type $type, DocumentProvider $documentProvider, $mapping)
    {
        if (!is_array($mapping) && !$mapping instanceof MappingProvider && !$mapping instanceof Mapping) {
            throw new \InvalidArgumentException('The mapping must be an array, an instance of Zenstruck\ElasticaBundle\Elastica\MappingProvider or an instance of Elastica\Type\Mapping.');
        }

        $this->type = $type;
        $this->documentProvider = $documentProvider;
        $this->mapping = $mapping;
    }

    /**
     * @return Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return array|Mapping
     */
    public function getMapping()
    {
        if ($this->mapping instanceof MappingProvider) {
            return $this->mapping->getMapping();
        }

        return $this->mapping;
    }

    /**
     * @return Document[]
     */
    public function getDocuments()
    {
        return $this->documentProvider->getDocuments();
    }
}
