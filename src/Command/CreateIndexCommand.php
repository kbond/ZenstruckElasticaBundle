<?php

namespace Zenstruck\ElasticaBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Zenstruck\ElasticaBundle\Elastica\IndexManager;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class CreateIndexCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('zenstruck:elastica:create')
            ->setDescription('Create and populate the elasticsearch index.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var IndexManager $indexManager */
        $indexManager = $this->getContainer()->get('zenstruck_elastica.index_manager');

        $indexManager->create();
    }
}
