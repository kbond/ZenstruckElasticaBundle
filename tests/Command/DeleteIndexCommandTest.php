<?php

namespace Zenstruck\ElasticaBundle\Tests\Command;

use Zenstruck\ElasticaBundle\Command\DeleteIndexCommand;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class DeleteIndexCommandTest extends BaseCommandTest
{
    protected function getManagerMethodName()
    {
        return 'delete';
    }

    protected function getCommandName()
    {
        return 'zenstruck:elastica:delete';
    }

    protected function createCommand()
    {
        return new DeleteIndexCommand();
    }
}
