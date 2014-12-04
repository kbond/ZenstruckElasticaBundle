<?php

namespace Zenstruck\ElasticaBundle;

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
