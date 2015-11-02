<?php

namespace Zenstruck\ElasticaBundle\Command;

use Elastica\Request;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Zenstruck\ElasticaBundle\Elastica\IndexContext;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class PopulateIndexCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('zenstruck:elastica:populate-index')
            ->setDescription('Populates an index.')
            ->addOption('chunk-size', null, InputOption::VALUE_REQUIRED, 'The number of documents to add at once.', 500);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var IndexContext $indexContext */
        $indexContext = $this->getContainer()->get('zenstruck_elastica.index_context');
        $index = $indexContext->getIndex();

        if (!$index->exists()) {
            throw new \RuntimeException('Index does not exist.');
        }

        $output->writeln('Purging and optimizing index.');
        $index->request('_query', Request::DELETE, array(), array('q' => '*:*'));
        $index->request('_optimize', Request::POST, array(), array(
                'wait_for_merge' => 'false',
                'only_expunge_deletes' => 'true',
            )
        );

        if ($settings = $indexContext->getSettings()) {
            $output->writeln('Configuring index settings.');
            $index->close();
            $index->setSettings($settings);
            $index->open();
        }

        foreach ($indexContext->getTypeContexts() as $alias => $typeContext) {
            $type = $typeContext->getType();
            $output->writeln(sprintf('Configuring type <comment>%s</comment>.', $alias));

            if ($type->exists()) {
                $output->writeln('  Deleting.');
                $type->delete();

                // delay to ensure type is deleted
                sleep(2);
            }

            $output->writeln('  Creating mapping.');
            $type->setMapping($typeContext->getMapping());

            $documents = $typeContext->getDocuments();
            $total = count($documents);
            $count = 0;
            $output->writeln(sprintf('  Adding <info>%d</info> documents.', $total));

            foreach (array_chunk($documents, $input->getOption('chunk-size')) as $chunks) {
                $count += count($chunks);
                $type->addDocuments($chunks);
                $output->writeln(sprintf('    %s%%', ceil($count / $total * 100)));
            }

            $output->writeln('  Done.');
        }

        $output->writeln('Refreshing index.');
        $index->refresh();
    }
}
