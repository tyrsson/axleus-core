<?php

declare(strict_types=1);

namespace Axleus;

use Laminas\EventManager\EventManager;
use Laminas\EventManager\SharedEventManager;
use Laminas\EventManager\SharedEventManagerInterface;
use Psr\Container\ContainerInterface;

final class EventManagerFactory
{
    public function __invoke(ContainerInterface $container): EventManager
    {
        return new EventManager(
            $container->has(SharedEventManagerInterface::class) ? $container->get(SharedEventManagerInterface::class) : new SharedEventManager(),
        );
    }
}
