<?php

declare(strict_types=1);

namespace Axleus\CommandBus;

//use League\Tactician\CommandEvents\Event\CommandFailed;
//use League\Tactician\CommandEvents\Event\CommandHandled;
//use League\Tactician\CommandEvents\EventMiddleware;
use Laminas\EventManager\EventManagerInterface;
use League\Tactician\Plugins\NamedCommand\NamedCommand;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Monolog\Logger;

final class EventMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): Event\EventMiddleware
    {
        /**
         * This will most likely end up being moved to a listener class so that
         * we end up with one listener class per channel
         */
        /** @var Logger $logger */
        $logger = $container->get(LoggerInterface::class);
        $logger = $logger->withName('command-bus'); // set the channel
        // remove this and pull EventManager
        $em = $container->get(EventManagerInterface::class);
        $events = new Event\EventMiddleware();

        // changes to attach
        $em->attach(
            // use const
            Event\CommandEventInterface::COMMAND_HANDLED_EVENT,
            // use new CommandHanlded event
            function (Event\CommandHandled $event) use ($logger) {
                /** @var NamedCommand */
                $handled = $event->getCommand();
                $logger->info(
                    'Handled {command} successfully.', // success message
                    [
                        'command' => $handled->getCommandName(),
                    ]
                );
                $logger->close();
            }
        );
        $em->attach(
            Event\CommandEventInterface::COMMAND_FAILED_EVENT,
            function (Event\CommandFailed $event) use ($logger) {
                /** @var NamedCommand */
                $failed = $event->getCommand();
                $logger->info(
                    '{command} failed.', // failure message
                    [
                        'command' => $failed->getCommandName(),
                    ]
                );
                $logger->close();
            }
        );
        $events->setEventManager($em);
        return $events;
    }
}

