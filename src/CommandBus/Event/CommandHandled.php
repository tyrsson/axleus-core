<?php

declare(strict_types=1);

namespace Axleus\CommandBus\Event;

use Axleus\CommandBus\Event\CommandEventInterface;
use Laminas\EventManager\Event;

final class CommandHandled extends Event implements CommandEventInterface
{
    use HasCommandTrait;

    /**
     *
     * @param string $name
     * @param string|object|null $target
     * @psalm-param TTarget $target
     * @param array|ArrayAccess|object|null $params
     * @psalm-param TParams|array<empty,empty>|null $params
     */
    public function __construct(
        protected $target = null
    ) {
        parent::__construct(CommandEventInterface::COMMAND_HANDLED_EVENT);
    }
}
