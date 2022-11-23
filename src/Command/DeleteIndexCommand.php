<?php

namespace Zenstruck\ElasticaBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class DeleteIndexCommand extends IndexCommand
{
    protected function configure(): void
    {
        $this
            ->setName('zenstruck:elastica:delete')
            ->setDescription('Delete the elasticsearch index.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->indexManager->delete();

        return 0;
    }
}
