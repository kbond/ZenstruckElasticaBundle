<?php

namespace Zenstruck\ElasticaBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ReindexIndexCommand extends IndexCommand
{
    protected static $defaultName = 'zenstruck:elastica:reindex';

    protected function configure()
    {
        $this->setDescription('Reindex the elasticsearch index.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->indexManager->reindex();

        return 0;
    }
}
