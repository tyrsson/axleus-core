<?php

declare(strict_types=1);

namespace Axleus\Whoops;

use Psr\Container\ContainerInterface;
use Whoops\Handler\PrettyPageHandler;

final class WhoopsPageHandlerDelegatorFactory
{
    public function __invoke(ContainerInterface $container, $name, callable $callback): PrettyPageHandler
    {
        $handler = $callback();
        $handler->addDataTable('Application Configuration', ['Merged Config' => $container->get('config')]);
        return $handler;
    }
}
