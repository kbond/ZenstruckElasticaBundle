<?php

namespace Zenstruck\ElasticaBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class DeleteIndexCommand extends IndexCommand
{
    protected static $defaultName = 'zenstruck:elastica:delete';

    protected function configure()
    {
        $this->setDescription('Delete the elasticsearch index.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->indexManager->delete();

        return 0;
    }
}
