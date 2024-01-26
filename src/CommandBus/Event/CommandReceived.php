<?php

declare(strict_types=1);

namespace Axleus\CommandBus\Event;

use Axleus\CommandBus\Event\CommandEventInterface;
use Laminas\EventManager\Event;

final class CommandReceived extends Event
{
    /**
     *
     * @param string $name
     * @param string|object|null $target
     * @psalm-param TTarget $target
     * @param array|ArrayAccess|object|null $params
     * @psalm-param TParams|array<empty,empty>|null $params
     */
    public function __construct(
        $name = CommandEventInterface::COMMAND_RECEIVED_EVENT,
        $target = null,
        $params = []
    ) {
        parent::__construct($name, $target, $params);
    }
}
