<?php

namespace Zenstruck\ElasticaBundle\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class BaseCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function can_execute()
    {
        $indexManager = $this->getMock('Zenstruck\ElasticaBundle\Elastica\IndexManager', array($this->getManagerMethodName()), array(), '', false);
        $indexManager->expects($this->once())
            ->method($this->getManagerMethodName())
        ;

        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $container->expects($this->once())
            ->method('get')
            ->with('zenstruck_elastica.index_manager')
            ->willReturn($indexManager);

        $kernel = $this->getMock('Symfony\Component\HttpKernel\KernelInterface');
        $kernel->expects($this->once())
            ->method('getContainer')
            ->willReturn($container);

        $application = new Application($kernel);

        $command = $this->createCommand();
        $command->setContainer($container);
        $application->add($this->createCommand());

        $tester = new CommandTester($application->find($this->getCommandName()));
        $tester->execute(array('command' => $this->getCommandName()));

        $this->assertSame('', $tester->getDisplay());
    }

    /**
     * @return string
     */
    abstract protected function getManagerMethodName();

    /**
     * @return string
     */
    abstract protected function getCommandName();

    /**
     * @return ContainerAwareCommand
     */
    abstract protected function createCommand();
}
