<?php

namespace Zenstruck\ElasticaBundle\Tests\Command;

use Mockery as m;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Zenstruck\ElasticaBundle\Command\PopulateIndexCommand;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class PopulateIndexCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function can_execute()
    {
        $index = m::mock('Elastica\Index');
        $index->shouldReceive('exists')->andReturn(true);
        $index->shouldReceive('request')->twice();
        $index->shouldReceive('open')->once();
        $index->shouldReceive('setSettings')->with(array('settings'));
        $index->shouldReceive('close')->once();
        $index->shouldReceive('refresh')->once();

        $type = m::mock('Elastica\Type');
        $type->shouldReceive('exists')->andReturn(true);
        $type->shouldReceive('delete')->once();
        $type->shouldReceive('setMapping')->with(array('mapping'));
        $type->shouldReceive('addDocuments')->with(m::type('array'));

        $typeContext = m::mock('Zenstruck\ElasticaBundle\Elastica\TypeContext');
        $typeContext->shouldReceive('getType')->andReturn($type);
        $typeContext->shouldReceive('getMapping')->andReturn(array('mapping'));
        $typeContext->shouldReceive('getDocuments')->andReturn(array('foo', 'bar', 'baz'));

        $indexContext = m::mock('Zenstruck\ElasticaBundle\Elastica\IndexContext');
        $indexContext->shouldReceive('getIndex')->andReturn($index);
        $indexContext->shouldReceive('getTypeContexts')->andReturn(array('foo' => $typeContext));
        $indexContext->shouldReceive('getSettings')->andReturn(array('settings'));

        $container = m::mock('Symfony\Component\DependencyInjection\ContainerInterface');
        $container->shouldReceive('get')->with('zenstruck_elastica.index_context')->andReturn($indexContext);

        $command = new PopulateIndexCommand();
        $command->setContainer($container);

        $application = new Application();
        $application->add($command);
        $commandTester = new CommandTester($application->find('zenstruck:elastica:populate-index'));

        $commandTester->execute(
            array('command' => 'zenstruck:elastica:populate-index', '--chunk-size' => 1)
        );

        $this->assertContains('Purging and optimizing index.', $commandTester->getDisplay());
        $this->assertContains('Configuring index settings.', $commandTester->getDisplay());
        $this->assertContains('Configuring type foo.', $commandTester->getDisplay());
        $this->assertContains('Adding 3 documents.', $commandTester->getDisplay());
        $this->assertContains('Refreshing index.', $commandTester->getDisplay());
    }

    /**
     * @test
     *
     * @expectedException \RuntimeException
     */
    public function execute_fails_with_no_index()
    {
        $index = m::mock('Elastica\Index');
        $index->shouldReceive('exists')->andReturn(false);

        $indexContext = m::mock('Zenstruck\ElasticaBundle\Elastica\IndexContext');
        $indexContext->shouldReceive('getIndex')->andReturn($index);

        $container = m::mock('Symfony\Component\DependencyInjection\ContainerInterface');
        $container->shouldReceive('get')->with('zenstruck_elastica.index_context')->andReturn($indexContext);

        $command = new PopulateIndexCommand();
        $command->setContainer($container);

        $application = new Application();
        $application->add($command);
        $commandTester = new CommandTester($application->find('zenstruck:elastica:populate-index'));

        $commandTester->execute(
            array('command' => 'zenstruck:elastica:populate-index')
        );
    }
}
