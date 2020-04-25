<?php

namespace Zenstruck\ElasticaBundle\Command;

use Symfony\Component\Console\Command\Command;
use Zenstruck\ElasticaBundle\Elastica\IndexManager;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class IndexCommand extends Command
{
    protected $indexManager;

    public function __construct(IndexManager $indexManager)
    {
        parent::__construct();

        $this->indexManager = $indexManager;
    }
}
