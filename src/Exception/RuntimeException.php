<?php

namespace Zenstruck\ElasticaBundle\Exception;

use Elastica\Index;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class RuntimeException extends \RuntimeException implements Exception
{
    /**
     * @param Index $index
     *
     * @return RuntimeException
     */
    public static function indexExists(Index $index)
    {
        return new self(sprintf('Index "%s" already exists.', $index->getName()));
    }
}
