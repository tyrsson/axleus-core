<?php

declare(strict_types=1);

namespace Axleus\CommandBus\Event;

use Laminas\EventManager\Event;

final class CommandFailed extends Event implements CommandEventInterface
{
    use HasCommandTrait;

    /**
     * Checks whether exception is caught
     *
     * @var boolean
     */
    protected $exceptionCaught = false;

    public function __construct(
        protected $command,
        protected \Exception $exception
    ) {
        parent::__construct(self::COMMAND_FAILED_EVENT);
    }

    /**
     * Returns the exception
     *
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * Indicates that exception is caught
     */
    public function catchException()
    {
        $this->exceptionCaught = true;
    }

    /**
     * Checks whether exception is caught
     *
     * @return boolean
     */
    public function isExceptionCaught()
    {
        return $this->exceptionCaught;
    }
}
