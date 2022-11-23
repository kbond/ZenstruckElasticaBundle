<?php

namespace Zenstruck\ElasticaBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ReindexIndexCommand extends IndexCommand
{
    protected function configure(): void
    {
        $this
            ->setName('zenstruck:elastica:reindex')
            ->setDescription('Reindex the elasticsearch index.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->indexManager->reindex();

        return 0;
    }
}
