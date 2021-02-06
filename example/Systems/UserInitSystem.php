<?php declare(strict_types=1);

namespace Invariance\Ecs\Example\Systems;

use Invariance\Ecs\EcsContext;
use Invariance\Ecs\Example\Components\DatabaseConnectionComponent;
use Invariance\Ecs\Example\Components\UserComponent;
use Invariance\Ecs\Example\DatabaseConfig;
use Invariance\Ecs\System\EcsInitSystem;
use SleekDB\Store;

final class UserInitSystem implements EcsInitSystem
{
    private EcsContext $context;
    private DatabaseConfig $config;

    public function init(): void
    {
        $store = new Store('users', $this->config->getDataDir());

        // create user Entity
        $entity = $this->context->makeEntity();
        $entity->replace(new DatabaseConnectionComponent($store));
        $entity->replace(new UserComponent());
    }
}