<?php

namespace Zenstruck\ElasticaBundle;

use Elastica\Type;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class TypeContext
{
    private $type;
    private $documentProvider;
    private $mapping;

    public function __construct(Type $type, DocumentProvider $documentProvider, array $mapping = null)
    {
        if (null === $mapping && !$documentProvider instanceof MappingProvider) {
            throw new \InvalidArgumentException('A mapping must be provided as the 3rd argument or with a MappingProvider as the 2nd argument');
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
     * @return array|Type\Mapping
     */
    public function getMapping()
    {
        if ($this->documentProvider instanceof MappingProvider) {
            return $this->documentProvider->getMapping();
        }

        return $this->mapping;
    }

    /**
     * @return \Elastica\Document[]
     */
    public function getDocuments()
    {
        return $this->documentProvider->getDocuments();
    }
}
