<?php

declare(strict_types=1);

namespace Axleus\CommandBus;

abstract readonly class AbstractCommand implements CommandInterface
{
    private iterable $args;

    public function getCommandName()
    {
        return static::class;
    }
}
