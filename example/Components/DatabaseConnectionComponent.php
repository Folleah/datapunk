<?php declare(strict_types=1);

namespace Invariance\Ecs\Example\Components;

use Invariance\Ecs\EcsComponent;
use SleekDB\Store;

class DatabaseConnectionComponent implements EcsComponent
{
    public Store $store;

    public function __construct(Store $connection)
    {
        $this->store = $connection;
    }
}