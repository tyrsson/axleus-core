<?php

declare(strict_types=1);

namespace Axleus;

use Axleus\Middleware\DefaultParamsMiddleware;
use Laminas\Stratigility\Middleware\ErrorHandler;
use Mezzio\Handler\NotFoundHandler;
use Mezzio\Helper\ServerUrlMiddleware;
use Mezzio\Helper\UrlHelperMiddleware;
use Mezzio\Router\Middleware\DispatchMiddleware;
use Mezzio\Router\Middleware\ImplicitHeadMiddleware;
use Mezzio\Router\Middleware\ImplicitOptionsMiddleware;
use Mezzio\Router\Middleware\MethodNotAllowedMiddleware;
use Mezzio\Router\Middleware\RouteMiddleware;
use Mezzio\Session\SessionMiddleware;
///////////////
use League\Tactician\CommandEvents\EventMiddleware;
use League\Tactician\Plugins\NamedCommand\NamedCommandExtractor;
use Mezzio\Application;
use Mezzio\Container\ApplicationConfigInjectionDelegator;
use TacticianModule\Locator\ClassnameLaminasLocator;

final class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies'        => $this->getDependencies(),
            'middleware_pipeline' => $this->getPipelineConfig(),
            'routes'              => $this->getRoutes(),
            'tactician'           => $this->getTacticianConfig(),
        ];
    }

    public function getDependencies(): array
    {
        return [
            'delegators' => [
                Application::class => [
                    ApplicationConfigInjectionDelegator::class,
                ],
            ],
            'factories' => [
                Middleware\AjaxRequestMiddleware::class   => Middleware\AjaxRequestMiddlewareFactory::class,
                Middleware\DefaultParamsMiddleware::class => Middleware\DefaultParamsMiddlewareFactory::class,
                EventMiddleware::class                    => CommandBus\EventMiddlewareFactory::class,
            ],
            'invokables' => [
                Handler\PingHandler::class     => Handler\PingHandler::class,
                NamedCommandExtractor::class   => NamedCommandExtractor::class,
                ClassnameLaminasLocator::class => ClassnameLaminasLocator::class,
            ],
        ];
    }

    public function getPipelineConfig(): array
    {
        return [
            [// piped first
                'middleware' => ErrorHandler::class,
                'priority'   => 10006,
            ],
            [
                'middleware' => ServerUrlMiddleware::class,
                'priority'   => 10005,
            ],
            [
                'middleware' => SessionMiddleware::class,
                'priority'   => 10004,
            ],
            [// this must be in the pipeline or ajax request fail
                'middleware' => Middleware\AjaxRequestMiddleware::class,
                'priority'   => 10001,
            ],
            /**
             * Middleware that needs to run for all request should be piped here at a
             * priority of 10000 which means they will be piped in the other they are
             * discovered regardless of where they are piped from.
             * Piping order will be determined from the order of their ConfigProviders in
             * config.php
             * priority 10000 is intentionally skipped here for other services to use
             */
            [
                'middleware' => RouteMiddleware::class,
                'priority'   => 9999,
            ],
            [
                'middleware' => [// these are piped together at the same priority so they are piped in the order discovered
                    ImplicitHeadMiddleware::class,
                    ImplicitOptionsMiddleware::class,
                    MethodNotAllowedMiddleware::class,
                ],
                'priority'   => 9998,
            ],
            [
                'middleware' => UrlHelperMiddleware::class,
                'priority'   => 9997,
            ],
            /**
             * pipe middleware here that needs to introspect the routing result
             * Priority range 2000 - 5000
             */
            [
                'middleware' => DefaultParamsMiddleware::class,
                'priority'   => 1,
            ],
            [// dispatch at 0
                'middleware' => DispatchMiddleware::class,
                'priority'   => 0,
            ],
            [// pipe this VERY late so that everyone has a chance to respond before hitting it
                'middleware' => NotFoundHandler::class,
                'priority'   => -500,
            ],
        ];
    }

    public function getRoutes(): array
    {
        return [
            [
                'path'            => '/api/ping',
                'name'            => 'api.ping',
                'middleware'      => Handler\PingHandler::class,
                'allowed_methods' => ['GET'],
            ],
        ];
    }

    public function getTacticianConfig(): array
    {
        return [
            'default-extractor'  => NamedCommandExtractor::class,
            'middleware' => [
                EventMiddleware::class => 50,
            ],
        ];
    }
}