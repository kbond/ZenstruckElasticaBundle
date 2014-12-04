<?php

namespace Zenstruck\ElasticaBundle;

use Elastica\Document;
use Elastica\Type;

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
