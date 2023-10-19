<?php

declare(strict_types=1);

namespace Axleus;

use Axleus\Middleware\DefaultParamsMiddleware;
use Laminas\Stratigility\Middleware\ErrorHandler;
use League\Tactician\CommandEvents\EventMiddleware;
use League\Tactician\Plugins\NamedCommand\NamedCommandExtractor;
use Mezzio\Application;
use Mezzio\Handler\NotFoundHandler;
use Mezzio\Helper\ServerUrlMiddleware;
use Mezzio\Helper\UrlHelperMiddleware;
use Mezzio\Router\Middleware\DispatchMiddleware;
use Mezzio\Router\Middleware\ImplicitHeadMiddleware;
use Mezzio\Router\Middleware\ImplicitOptionsMiddleware;
use Mezzio\Router\Middleware\MethodNotAllowedMiddleware;
use Mezzio\Router\Middleware\RouteMiddleware;
use Mezzio\Session\SessionMiddleware;
use Psr\Container\ContainerInterface;
use TacticianModule\Locator\ClassnameLaminasLocator;

final class AxleusPipelineDelegatorFactory
{
    public function __invoke(
        ContainerInterface $container,
        string $serviceName,
        callable $callback
    ) : Application {
        /** @var $app Application */
        $app = $callback();

        // Setup pipeline:
        $app->pipe(
            [// piped first
                'middleware' => ErrorHandler::class,
                'priority'   => 10400,
            ]
        );
        $app->pipe(
            [
                'middleware' => ServerUrlMiddleware::class,
                'priority'   => 10300,
            ]
        );
        // session here
        $app->pipe(
            [
                'middleware' => SessionMiddleware::class,
                'priority'   => 10200,
            ]
        );
        // ajax request middleware
        $app->pipe(
            [// this must be in the pipeline or ajax request fail
                'middleware' => Middleware\AjaxRequestMiddleware::class,
                'priority'   => 10100,
            ]
        );
         /**
          * Middleware that needs to run for all request should be piped here at a
          * priority of 10000 which means they will be piped in the other they are
          * discovered regardless of where they are piped from.
          * Piping order will be determined from the order of their ConfigProviders in
          * config.php
          * priority 10000 is intentionally skipped here for other services to use
          */
        $app->pipe(
            [
                'middleware' => RouteMiddleware::class,
                'priority'   => 9999,
            ]
        );
        // $app->pipe(
        //     [
        //         'middleware' => [// these are piped together at the same priority so they are piped in the order discovered
        //             ImplicitHeadMiddleware::class,
        //             ImplicitOptionsMiddleware::class,
        //             MethodNotAllowedMiddleware::class,
        //         ],
        //         'priority'   => 9998,
        //     ]
        // );
        $app->pipe(
            [
                'middleware' => ImplicitHeadMiddleware::class,
                'priority'   => 9998,
            ]
        );
        $app->pipe(
            [
                'middleware' => ImplicitOptionsMiddleware::class,
                'priority'   => 9998,
            ]
        );
        $app->pipe(
            [
                'middleware' => MethodNotAllowedMiddleware::class,
                'priority'   => 9998,
            ]
        );
        $app->pipe(
            [
                'middleware' => UrlHelperMiddleware::class,
                'priority'   => 9997,
            ]
        );
        /**
         * pipe middleware here that needs to introspect the routing result
         * Priority range 2000 - 5000
         */
        // default params middleware
        $app->pipe(
            [
                'middleware' => DefaultParamsMiddleware::class,
                'priority'   => 1,
            ]
        );
        $app->pipe(
            [// dispatch at 0
                'middleware' => DispatchMiddleware::class,
                'priority'   => 0,
            ]
        );

        $app->pipe(
            [// pipe this VERY late so that everyone has a chance to respond before hitting it
                'middleware' => NotFoundHandler::class,
                'priority'   => -500,
            ]
        );

        // Setup routes:
        // $app->get('/', Handler\HomePageHandler::class, 'home');
        // $app->get('/api/ping', Handler\PingHandler::class, 'api.ping');

        return $app;
    }
}
