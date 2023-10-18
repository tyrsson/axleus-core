<?php

declare(strict_types=1);

namespace Axleus;

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
            [// this must be in the pipeline or ajax request fail
                'middleware' => Middleware\AjaxRequestMiddleware::class,
                'priority' => 4,
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