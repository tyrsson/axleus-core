<?php

declare(strict_types=1);

namespace Axleus\CommandBus\Event;

interface CommandEventInterface
{
    public const COMMAND_FAILED_EVENT   = 'command.failed';
    public const COMMAND_HANDLED_EVENT  = 'command.handled';
    public const COMMAND_RECEIVED_EVENT = 'command.received';

    /**
     * Returns the command
     *
     * @return Command
     */
    public function getCommand();
}
