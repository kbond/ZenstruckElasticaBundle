<?php

namespace Zenstruck\ElasticaBundle\Tests\Command;

use Zenstruck\ElasticaBundle\Command\CreateIndexCommand;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class CreateIndexCommandTest extends BaseCommandTest
{
    protected function getManagerMethodName()
    {
        return 'create';
    }

    protected function getCommandName()
    {
        return 'zenstruck:elastica:create';
    }

    protected function createCommand()
    {
        return new CreateIndexCommand();
    }
}
