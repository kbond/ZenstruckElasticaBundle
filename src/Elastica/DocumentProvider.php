<?php

namespace Zenstruck\ElasticaBundle\Elastica;

use Elastica\Document;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface DocumentProvider
{
    /**
     * @return Document[]
     */
    public function getDocuments();
}
