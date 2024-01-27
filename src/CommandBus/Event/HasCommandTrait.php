<?php

declare(strict_types=1);

namespace Axleus\CommandBus\Event;

trait HasCommandTrait
{
    /**
     * Returns the command ($target)
     *
     * @return object
     */
    public function getCommand()
    {
        return $this->target;
    }
}
