<?php

namespace Zenstruck\ElasticaBundle;

use Elastica\Type\Mapping;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface MappingProvider
{
    /**
     * @return array|Mapping The mapping for the type
     */
    public function getMapping();
}
