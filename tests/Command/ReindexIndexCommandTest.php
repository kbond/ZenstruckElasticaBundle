<?php

namespace Zenstruck\ElasticaBundle\Tests\Command;

use Zenstruck\ElasticaBundle\Command\ReindexIndexCommand;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ReindexIndexCommandTest extends BaseCommandTest
{
    protected function getManagerMethodName()
    {
        return 'reindex';
    }

    protected function getCommandName()
    {
        return 'zenstruck:elastica:reindex';
    }

    protected function createCommand()
    {
        return new ReindexIndexCommand();
    }
}
