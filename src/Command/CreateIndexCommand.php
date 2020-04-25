<?php

namespace Zenstruck\ElasticaBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class CreateIndexCommand extends IndexCommand
{
    protected static $defaultName = 'zenstruck:elastica:create';

    protected function configure()
    {
        $this->setDescription('Create and populate the elasticsearch index.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->indexManager->create();

        return 0;
    }
}
