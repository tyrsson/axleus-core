<?php

declare(strict_types=1);

namespace Axleus\Storage;

use Laminas\Hydrator\ReflectionHydrator;
use Webinertia\Db;

class AbstractRepository implements Db\RepositoryInterface, Db\RepositoryCommandInterface
{
    use RepositoryTrait;

    public function __construct(
        private Db\TableGateway $gateway,
        private ReflectionHydrator $hydrator = new ReflectionHydrator(),
    ) {
    }
}
